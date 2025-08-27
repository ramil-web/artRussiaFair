<?php

namespace Admin\Services;

use Admin\Http\Filters\NameFilter;
use Admin\Repositories\Event\EventRepository;
use App\Enums\TimeSlotEnum;
use App\Exceptions\CustomException;
use App\Models\AppComment;
use App\Models\Broadcast;
use App\Models\ClassicEvent;
use App\Models\CommissionAssessment;
use App\Models\Event;
use App\Models\UserApplication;
use App\Models\UserApplicationImages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EventService
{
    const DELETE = 'delete';
    const ARCHIVE = 'archive';

    public function __construct(
        protected EventRepository $eventRepository,
        public Event              $event
    )
    {
    }


    /**
     * @throws Throwable
     * @throws CustomException
     */
    public function copyData(): void
    {
        $this->copyClassicDataToCommon();
    }

    public function list(array $appData)
    {
        /**
         * Temporarily , then don 't forget to delete
         */

        $withRelation = ['time_slots'];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('type'),
            AllowedFilter::exact('status'),
            AllowedFilter::exact('category'),
            AllowedFilter::exact('year'),
            AllowedFilter::custom('name', new NameFilter()),
            AllowedFilter::trashed()
        ];
        $allowedFields = [
            'id', 'name', 'description', 'social_links', 'place',
            'year', 'start_date', 'end_date', 'status', 'order_column'
        ];
        $allowedIncludes = [

        ];
        $allowedSorts = ['year'];

        $perPage = array_key_exists('per_page', $appData) ? $appData['per_page'] : null;

        return $this->eventRepository->getAllByFilters(
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $allowedSorts,
            $perPage
        );
    }


    /**
     * @param array $data
     * @return Model
     * @throws CustomException
     */
    public function create(array $data): Model
    {
        $event = $this->eventRepository->create($data);
        return $this->eventRepository->findById($event->id);
    }

    public function showById(int $id): ?Model
    {
        $withRelation = ['time_slots'];
        $allowedFields = [];
        $allowedIncludes = [];
        return $this->eventRepository->findById(
            $id,
            $withRelation,
            $allowedFields,
            $allowedIncludes,
        );
    }

    public function update(int $id, array $data): Model
    {
        $model = $this->eventRepository->findById($id);
        $this->eventRepository->updateEvent($model, $data);
        return $this->show($model->id);
    }

    /**
     * @param int $id
     * @param string $delete
     * @return mixed
     * @throws CustomException|Throwable
     */
    public function delete(int $id, string $delete): mixed
    {
        try {
            DB::beginTransaction();

            /**
             * Completely remove | softDelete
             */
            if ($delete == self::DELETE) {
                $deleted = $this->eventRepository->delete($id);
                $this->eventRepository->deleteEvetgable($id);
            } else {
                $deleted = $this->eventRepository->archive($id);
            }
            DB::commit();
            return $deleted;
        } catch (QueryException|Throwable $e) {
            DB::rollBack();
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return Collection|array
     * @throws CustomException
     */
    public function slots(int $id): Collection|array
    {
        return $this->eventRepository->slots($id);
    }

    /**
     * @throws CustomException
     */
    public function addCodes(array $data): Builder|Model|string
    {
        try {
            $values = collect($data)
                ->map(fn($item) => "SELECT '{$item['barcode']}'::varchar AS barcode, '{$item['product_id']}'::varchar AS product_id"
                )
                ->implode(' UNION ALL ');

            $sql = "WITH input(barcode, product_id)
            AS ($values)
            SELECT b.barcode, b.product_id
            FROM broadcasts b
            JOIN input i ON b.barcode = i.barcode AND b.product_id = i.product_id";

            $existing = collect(DB::select($sql));

            // Filtering existed barcode product_id pairs
            $existingPairs = $existing->map(fn($item) => $item->barcode . ':' . $item->product_id)->toArray();
            $filtered = collect($data)->filter(function ($item) use ($existingPairs) {
                return !in_array($item['barcode'] . ':' . $item['product_id'], $existingPairs);
            })->values();

            // Adding created_ad date
            $filtered = array_map(fn($item) => $item + ['created_at' => now()], $filtered->toArray());
            $model = new Broadcast();
            if (count($filtered) > 0) {
                return $model->query()->insert($filtered);
            }
            return "There is no new data, either the barcodes are repeated, or you are sending an empty array.";
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param Model|null $response
     * @return void
     */
    private function jsonDecode(?Model $response): void
    {
        $response->name = json_decode($response->name);
        $response->description = json_decode($response->description);
        $response->place = json_decode($response->place);
        $response->social_links = json_decode($response->social_links);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws CustomException
     */
    public function checkData(int $id): mixed
    {
        try {
            return $this->event->onlyTrashed()->find($id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param int $id
     * @return mixed
     * @throws CustomException
     */
    public function restore(int $id): mixed
    {
        try {
            $model = $this->checkData($id);
            return $model->restore();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }


    /**
     * @param string $id
     * @return Model|null
     */
    public function show(string $id): ?Model
    {
        $withRelation = ['time_slots'];
        $allowedFields = [];
        $allowedIncludes = [];
        $response = $this->eventRepository->findById(
            $id,
            $withRelation,
            $allowedFields,
            $allowedIncludes,
        );
        $intervals = $this->timeSlots($response->time_slots->toArray(), $response->id);
        unset($response->time_slots);
        $response->time_slots = $intervals['slots'];
        return $response;
    }

    /**
     * @param array $response
     * @param int $eventId
     * @return array
     */
    private function timeSlots(array $response, int $eventId): array
    {
        $checkIn = [];
        $exit = [];
        foreach ($response as $val) {
            if ($val['action'] == TimeSlotEnum::CHECK_IN()) {
                $checkIn[] = $val;
            } else {
                $exit[] = $val;
            }
        }
        if (!empty($checkIn)) {
            $intervals['check_in'] = [
                'start'    => array_shift($response)['date'] . ' ' . array_shift($response)['interval_times'],
                'end'      => end($response)['date'] . ' ' . end($response)['interval_times'],
                'event_id' => end($response)['event_id']
            ];
        } else {
            $intervals['check_in'] = null;
        }

        if (!empty($exit)) {
            $intervals['exit'] = [
                'start'    => array_shift($response)['date'] . ' ' . array_shift($response)['interval_times'],
                'end'      => end($response)['date'] . ' ' . end($response)['interval_times'],
                'event_id' => end($response)['event_id']
            ];
        } else {
            $intervals['exit'] = null;
        }
        return [
            'event_id' => $eventId,
            'slots'    => $intervals
        ];
    }

    /**
     * @throws Throwable
     * @throws CustomException
     */
    private function copyClassicDataToCommon(): void
    {

        try {
            $number = 0;
            $classicEvents = ClassicEvent::query()
                ->with([
                    'classicUserApplications.classicComments',
                    'classicUserApplications.classicAssessments',
                    'classicUserApplications.classicImages'
                ])
                ->get();

            DB::beginTransaction();
            $countUserApps = UserApplication::query()->count();
            foreach ($classicEvents as $key => $event) {
                $events = [
                    'name'                         => is_array($event->name) ? json_encode($event->name) : $event->name,
                    'description'                  => is_array($event->description) ? json_encode($event->description) : $event->description,
                    'social_links'                 => is_array($event->social_links) ? json_encode($event->social_links) : $event->social_links,
                    'year'                         => $event->year,
                    'start_date'                   => $event->start_date,
                    'end_date'                     => $event->end_date,
                    'status'                       => $event->status,
                    'slug'                         => $event->slug . 'classic',
                    'sort_id'                      => $event->sort_id,
                    'start_accepting_applications' => $event->start_accepting_applications,
                    'end_accepting_applications'   => $event->end_accepting_applications,
                    'place'                        => is_array($event->place) ? json_encode($event->place) : $event->place,
                    'event_type'                   => $event->event_type,
                    'category'                     => 'classic',
                    'created_at'                   => $event->created_at,
                    'updated_at'                   => $event->updated_at,
                ];

                if (Event::query()->insert($events)) {
                    foreach ($event->toArray()['classic_user_applications'] as $app) {

                        $ap = [
                            'type'                   => $app['type'],
                            'name_gallery'           => $app['name_gallery'],
                            'representative_name'    => $app['representative_name'],
                            'representative_surname' => $app['representative_surname'],
                            'representative_email'   => $app['representative_email'],
                            'representative_phone'   => $app['representative_phone'],
                            'representative_city'    => $app['representative_city'],
                            'about_style'            => $app['about_style'],
                            'about_description'      => $app['about_description'],
                            'other_fair'             => $app['other_fair'],
                            'social_links'           => $app['social_links'],
                            'visitor'                => $app['visitor'],
                            'event_slug'             => $event->slug . 'classic',
                        ];

                        $event_s = Event::query()->where('slug', $event->slug . 'classic')->select(['id', 'slug'])->get()->toArray();
                        if ($event_s[0]['slug'] == $ap['event_slug']) {
                            $ap['event_id'] = $event_s[0]['id'];

                            $number = 'AR-' . date("Y") . '-' . $app['type'] . '-' . auth()->user()->id . '-' . $countUserApps . $event_s[0]['id'];
                            $ap['number'] = $number;
                        }
                        unset($ap['event_slug']);

                        if (UserApplication::query()->create($ap)) {
                            $userApps = UserApplication::query()->where('number', $number)->select(['id', 'number'])->get()->toArray();


                            foreach ($app['classic_comments'] as $com) {
                                $comment = [
                                    'user_id' => $com['user_id'],
                                    'message' => $com['message'],
                                    'number'  => $number
                                ];


                                foreach ($userApps as $a) {
                                    if ($a['number'] == $comment['number']) {
                                        $comment['user_application_id'] = $a['id'];
                                    }
                                }
                                unset($comment['number']);
                                AppComment::query()->create($comment);
                            }


                            foreach ($app['classic_assessments'] as $asses) {
                                $assessment = [
                                    'user_id' => $asses['user_id'],
                                    'status'  => $asses['status'],
                                    'comment' => $asses['comment'],
                                    'number'  => $number
                                ];

                                foreach ($userApps as $a) {
                                    if ($a['number'] == $assessment['number']) {
                                        $assessment['user_application_id'] = $a['id'];
                                    }
                                }
                                unset($assessment['number']);
                                CommissionAssessment::query()->create($assessment);
                            }


                            foreach ($app['classic_images'] as $im) {
                                $image = [
                                    'url'         => $im['url'],
                                    'title'       => $im['title'],
                                    'description' => $im['description'],
                                    'number'      => $number
                                ];

                                foreach ($userApps as $a) {
                                    if ($a['number'] == $image['number']) {
                                        $image['user_application_id'] = $a['id'];
                                    }
                                }
                                unset($image['number']);
                                UserApplicationImages::query()->create($image);
                            }
                        }
                    }
                }
                DB::commit();
            }
            return;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new CustomException($e->getMessage(), 500);
        }
    }

}
