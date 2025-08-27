<?php

namespace Lk\Services;

use Admin\Events\AdminMessageSent;
use App\Enums\AppStatusEnum;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Lk\Http\Filters\CategoryFilter;
use Lk\Repositories\UserApplication\UserApplicationImageRepository;
use Lk\Repositories\UserApplication\UserApplicationRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserApplicationService
{
    private UserApplicationRepository $userApplicationRepository;
    private UserApplicationImageRepository $userApplicationImageRepository;


    /**
     * @param UserApplicationRepository $userApplicationRepository
     * @param UserApplicationImageRepository $userApplicationImageRepository
     */
    public function __construct(
        UserApplicationRepository      $userApplicationRepository,
        UserApplicationImageRepository $userApplicationImageRepository
    )
    {
        $this->userApplicationRepository = $userApplicationRepository;
        $this->userApplicationImageRepository = $userApplicationImageRepository;
    }

    /**
     * @param array $appData
     * @return Collection|QueryBuilder[]
     */
    public function list(array $appData): Collection|array
    {
        $where = [
            'name'  => 'user_id',
            'value' => Auth::id()
        ];
        $withRelation = ['images', 'events', 'visualization'];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('type'),
            AllowedFilter::exact('status'),
            AllowedFilter::custom('category', new CategoryFilter()),
        ];
        $allowedFields = [
            'id',
            'type',
            'name_gallery',
            'representative_name',
            'representative_surname',
            'representative_email',
            'representative_phone',
            'representative_city',
            'about_style',
            'about_description',
            'other_fair',
            'social_links',
            'status',
            'created_at',
            'updated_at',
            'time_slot_start_id',
            'visitor',
            'education'
        ];
        $allowedIncludes = [
            'images',
            'events'
        ];

        $allowedSorts = ['id', 'status', 'created_at', 'updated_at'];

        $withTrashed = false;

        $perPage = array_key_exists('per_page', $appData) ? $appData['per_page'] : null;
        return $this->userApplicationRepository->getAllByFilters(
            $where,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $allowedSorts,
            $withTrashed,
            $perPage,
        );
    }

    /**
     * @param array $data
     * @return Model
     * @throws CustomException
     */
    public function create(array $data): Model
    {
        //Сделать проверку, если это не первая заявка, добавить цифру к номеру
        $userApps = $this->userApplicationRepository->getUserAppByEventCategory($data['event_id']);
        $data['number'] = 'AR-' . date("Y") . '-' . $data['type'] . '-' . auth()->user()->id . '-' . count($userApps) . $data['event_id'];

        $data['status'] = AppStatusEnum::NEW();

        $application = $this->userApplicationRepository->createFromArray($data);

        $dataImage = $data['image'];

        if (count($dataImage) !== 0) {
            foreach ($dataImage as $image) {
                $image['user_application_id'] = $application->id;
                $this->userApplicationImageRepository->create($image);
            }
        }
        return $this->userApplicationRepository->findById($application->id);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        $data['visitor'] = [];
        $userApp = $this->userApplicationRepository->findById($id);
        $this->userApplicationRepository->update($userApp, $data);

        $dataImage = $data['image'];

        if (count($dataImage) !== 0) {
            foreach ($dataImage as $image) {
                $image['user_application_id'] = $id;
                $this->userApplicationImageRepository->updateImage($image);
            }
        }

        $this->userApplicationImageRepository->cleanImage($id, Arr::pluck($dataImage, 'url'));
        $userApp = $this->userApplicationRepository->findById($id);

        if ($userApp->status == AppStatusEnum::WAITING_AFTER_EDIT()->value) {
            broadcast(new AdminMessageSent([
                'id'     => $userApp['id'],
                'status' => $userApp['status']
            ]))->toOthers();
        }

        return $userApp;
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Model|null
     */
    public function show(int $id, Request $request)
    {
        $withRelation = ['images', 'visualization'];
        $allowedFields = [
            'id',
            'type',
            'name_gallery',
            'representative_name',
            'representative_surname',
            'representative_email',
            'representative_phone',
            'representative_city',
            'about_style',
            'about_description',
            'other_fair',
            'social_links',
            'status',
            'education'
        ];
        $allowedIncludes = [
            'images',
        ];
        $request->has('with_trashed') ? $withTrashed = true : $withTrashed = false;

        return $this->userApplicationRepository->findById(
            $id,
            $withRelation,
            $allowedFields,
            $allowedIncludes,
            $withTrashed
        );
    }

    /**
     * @param int $id
     * @return bool
     */
    public function checkStatus(int $id): bool
    {
        $userApp = $this->userApplicationRepository->findById($id);
        return $userApp->status !== AppStatusEnum::REJECTED()->value;
    }


    /**
     * @param string $category
     * @return Builder|Model|string
     */
    public function getStatus(string $category): Model|Builder|string
    {
        $status = $this->userApplicationRepository->getStatus($category);
        if (!$status) {
            return '';
        }

        if ($status->active === false) {
            return 'У вас нет активных заявок';
        }
        return $status;
    }

    /**
     * @throws CustomException
     */
    public function checkApp(int $eventId): bool
    {
        $userApps = $this->userApplicationRepository->getUserAppByEventCategory($eventId);
        foreach ($userApps as $app) {
            if ($app->status !== AppStatusEnum::REJECTED()->value) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param int $id
     * @return bool
     * @throws CustomException
     */
    public function checkIsNew(int $id): bool
    {
        return $this->userApplicationRepository->checkIsNew($id);
    }

    /**
     * @throws CustomException
     */
}
