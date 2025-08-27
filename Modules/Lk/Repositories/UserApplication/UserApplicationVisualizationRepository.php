<?php

namespace Lk\Repositories\UserApplication;

use App\Exceptions\CustomException;
use App\Models\UserApplication;
use App\Models\Visualization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Lk\Events\LkVisualizationUpdatedEvent;
use Lk\Repositories\BaseRepository;
use Storage;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UserApplicationVisualizationRepository extends BaseRepository
{

    public function __construct(Visualization $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $Data
     * @return Model
     * @throws CustomException|Throwable
     */
    public function create(array $Data): Model
    {
        try {
            DB::beginTransaction();
            $visualization = $this->model->query()
                ->create($Data);

            /**
             * When we created visualization, we cleared the user_application's visitor array
             */
            $this->clearVisitors($visualization->user_application_id);

            /**
             * We broadcast the event for automatic updates in the admin panel
             */
            broadcast(new LkVisualizationUpdatedEvent($visualization))->toOthers();

            DB::commit();

            return $visualization;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Model $created
     */
    public function jsonDecode(Model &$created): void
    {
        $created['url'] = json_decode($created['url']);
    }

    /**
     * @param int $id
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function show(int $id): Model|Collection|Builder|array|null
    {
        try {
            return $this->model
                ->query()
                ->findOrFail($id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return bool|mixed|null
     * @throws CustomException|Throwable
     */
    public function delete(int $id): mixed
    {
        try {
            DB::beginTransaction();

            /**
             * Delete images from storage? not only url
             */
            $urls = $this->getUrls($id);
            foreach ($urls as $url) {
                $this->deleteFromStore($url);
            }

            $visualisation = $this->show($id);
            $userApplicationId = $visualisation->user_application_id;

            /**
             * When we deleted visualization, we cleared the user_application's visitor array
             */
            $this->clearVisitors($userApplicationId);

            $response = $visualisation->forceDelete();
            DB::commit();

            return $response;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param mixed $dataApp
     * @return Builder|Builder[]|Collection|Model
     * @throws CustomException|Throwable
     */
    public function updateData(mixed $dataApp): Model|Collection|Builder|array
    {
        try {
            DB::beginTransaction();
            if (array_key_exists('url', $dataApp)) {
                $dataApp['url'] = array_values($this->checkUrls($dataApp['url'], $dataApp['id']));
            }
            $model = $this->model->query()->findOrFail($dataApp['id']);
            $this->update($model, $dataApp);

            /**
             * When we updated visualization, we cleared the user_application's visitor array
             */
            $this->clearVisitors($dataApp['user_application_id']);

            $visualization = $this->show($dataApp['id']);

            /**
             * We broadcast the event for automatic updates in the admin panel
             */
            broadcast(new LkVisualizationUpdatedEvent($visualization))->toOthers();

            DB::commit();

            return $visualization;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $url
     * @param int $id
     * @return array
     * @throws CustomException
     */
    private function checkUrls(array $url, int $id): array
    {
        try {
            return $this->arrayDiff($this->getUrls($id), $url);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $arr1
     * @param $arr2
     * @return array
     */
    private function arrayDiff($arr1, $arr2): array
    {
        $merged = array_merge($arr1, $arr2);
        return array_map("unserialize", array_unique(array_map("serialize", $merged)));
    }

    /**
     * @param int $id
     * @return array
     * @throws CustomException
     */
    private function getUrls(int $id): array
    {
        try {
            $existed = [];
            $model = $this->show($id)->url ?? [];

            foreach ($model as $url) {
                $existed[] = $url;
            }
            return $existed;
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $appData
     * @return bool
     * @throws CustomException
     * @throws Throwable
     */
    public function deleteImage(array $appData): bool
    {
        try {
            DB::beginTransaction();

            $model = $this->show($appData['id']);
            $urls = $model->url ?? [];

            if (($key = array_search($appData['image'], $urls)) !== false) {
                unset($urls[$key]);
            }
            $this->deleteFromStore($appData['image']);
            $visualization = $this->update($model, ['url' => $urls]);

            /**
             * When we deleted visualization image, we cleared the user_application's visitor array
             */
            $this->clearVisitors($model->user_application_id);


            /**
             * We broadcast the event for automatic updates in the admin panel
             */
            broadcast(new LkVisualizationUpdatedEvent($this->show($appData['id'])))->toOthers();

            DB::commit();

            return $visualization;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param mixed $url
     * @return void
     */
    private function deleteFromStore(mixed $url): void
    {
        Storage::delete($url);
    }

    /**
     * @param int $userApplicationId
     * @return int|bool
     * @throws CustomException
     */
    private function clearVisitors(int $userApplicationId): int|bool
    {
        try {
            return UserApplication::query()
                ->findOrFail($userApplicationId)
                ->update(['visitor' => []]);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $userApplicationId
     * @return bool
     * @throws CustomException
     */
    public function checkVisualization(int $userApplicationId): bool
    {
        try {
            return $this->model->query()
                ->where('user_application_id', $userApplicationId)
                ->exists();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
