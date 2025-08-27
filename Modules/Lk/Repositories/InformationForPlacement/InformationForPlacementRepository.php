<?php

namespace Lk\Repositories\InformationForPlacement;

use App\Exceptions\CustomException;
use App\Models\InformationForPlacement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Lk\Repositories\BaseRepository;
use Storage;
use Symfony\Component\HttpFoundation\Response;

class InformationForPlacementRepository extends BaseRepository
{
    public function __construct(InformationForPlacement $model)
    {
        parent::__construct($model);
    }

    public function createData(array $appData): Model
    {
        return $this->create($appData);
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
     * @param mixed $dataApp
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function updateData(mixed $dataApp): Model|Collection|Builder|array|null
    {
        try {
            $model = $this->findById($dataApp['id']);
            $this->update($model, $dataApp);
            return $this->findById($dataApp['id']);
        } catch (QueryException $e) {
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
     * @throws CustomException
     */
    public function delete(mixed $id)
    {
        try {

            /**
             * Delete images from storage, not only url
             */
            $urls = $this->getUrls($id);
            foreach ($urls as $url) {
                Storage::delete($url);
            }
            $model = $this->model
                ->query()
                ->findOrFail($id);

            /**
             * Delete photo from storage, not only url
             */
            if ($model->photo) {
                Storage::delete($model->photo);
            }
            return $model->forceDelete();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
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
            $model = $this->model->query()->find($id)->url ?? [];

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
     * @return array|Builder|Collection|Model
     * @throws CustomException
     */
    public function deleteImage(array $appData): array|Builder|Collection|Model
    {
        try {
            $model = $this->model->query()->findOrFail($appData['id']);
            $urls = $model->url ?? [];

            if (($key = array_search($appData['image'], $urls)) !== false) {
                unset($urls[$key]);
            }
            Storage::delete($appData['image']);
            $this->update($model, ['url' => $urls]);
            return $this->show($model->id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
