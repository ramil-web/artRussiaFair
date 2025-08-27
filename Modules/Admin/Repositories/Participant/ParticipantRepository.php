<?php

namespace Admin\Repositories\Participant;

use Admin\Repositories\BaseRepository;
use App\Enums\ParticipantTypesEnum;
use App\Exceptions\CustomException;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class ParticipantRepository extends BaseRepository
{
    public function __construct(Participant $model)
    {
        parent::__construct($model);
    }

    public function create(array $Data): Model
    {
        $this->jsonEncode($Data);
        return $this->model->create($Data);
    }

    /**
     * @param int $modelId
     * @param ParticipantTypesEnum $type
     * @return Model|null
     * @throws CustomException
     */
    public function findByIdAndTypeParticipant(
        int                  $modelId,
        ParticipantTypesEnum $type
    ): ?Model
    {
        return QueryBuilder::for($this->model)
            ->where('type', $type)
            ->withTrashed()
            ->find($modelId);
    }

    /**
     * @param int $id
     * @param array $Data
     * @return bool
     * @throws CustomException
     */
    public function updateParticipant(int $id, array $Data): bool
    {
        try {
            if (array_key_exists('images', $Data)) {
                $Data['images'] = array_values($this->checkImages($Data['images'], $id));
            }
            $this->jsonEncode($Data);
            $model = $this->model->withTrashed()->find($id);
            return $model->update($Data) && $model->trashed() ? $model->delete() : true;
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @param ParticipantTypesEnum $type
     * @return bool|mixed|null
     */
    public function delete(int $id, ParticipantTypesEnum $type): mixed
    {
        $query = QueryBuilder::for($this->model);
        return $query->where([
            'type' => $type
        ])
            ->withTrashed()
            ->findOrFail($id)
            ->forceDelete();
    }

    public function archive(int $id, ParticipantTypesEnum $type): mixed
    {
        $query = QueryBuilder::for($this->model);
        return $query->where([
            'type' => $type
        ])
            ->findOrFail($id)
            ->delete();
    }

    /**
     * @param array $Data
     * @return void
     */
    private function jsonEncode(array &$Data): void
    {
        if (array_key_exists('images', $Data)) {
            $Data['images'] = json_encode($Data['images']);
        }
        if (array_key_exists('name', $Data)) {
            $Data['name'] = json_encode($Data['name']);
        }
        if (array_key_exists('description', $Data)) {
            $Data['description'] = json_encode($Data['description']);
        }
    }

    /**
     * @param array $images
     * @param int $id
     * @return array
     * @throws CustomException
     */
    private function checkImages(array $images, int $id): array
    {
        try {
            return $this->arrayDiff($this->getImages($id), $images);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return array
     * @throws CustomException
     */
    private function getImages(int $id): array
    {
        try {
            $existed = [];
            $model = json_decode($this->model->withTrashed()->find($id)->images) ?? [];

            foreach ($model as $image) {
                $existed[] = $image;
            }
            return $existed;
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
     * @param mixed $image
     * @return true
     * @throws CustomException
     */
    public function deleteImage(int $id, mixed $image): bool
    {
        try {
            $images = $this->getImages($id);
            foreach ($images as $key => $val) {
                if ($val === $image) {
                    unset($images[$key]);
                }
            }
            $changed = array_values($images);
            $this->jsonEncode($changed);
            $model = $this->model->withTrashed()->find($id);

            $path = strstr($image, '/uploads');

            /**
             * Delete from storage also
             */
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
            return $model->update(['images' => $changed]) && $model->trashed() ? $model->delete() : true;
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
