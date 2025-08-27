<?php

namespace Admin\Repositories\Speaker;

use Admin\Repositories\BaseRepository;
use App\Enums\SpeakerTypesEnum;
use App\Models\Speaker;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

class SpeakerRepository extends BaseRepository
{
    public function __construct(Speaker $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $Data
     * @return Model
     */
    public function create(array $Data): Model
    {
        return $this->model->create($Data);
    }

    /**
     * @param int $id
     * @param array $Data
     * @return bool
     */
    public function updateSpeaker(int $id, array $Data): bool
    {
        $this->jsonEncode($Data);
        $model = $this->model->withTrashed()->find($id);

        return $model->update($Data) && $model->trashed() ? $model->delete() : true;
    }

    /**
     * @param int $modelId
     * @param SpeakerTypesEnum $type
     * @return Model|null
     */
    public function findByIdAndTypeSpeaker(
        int              $modelId,
        SpeakerTypesEnum $type
    ): ?Model
    {
        return QueryBuilder::for($this->model)
            ->withTrashed()
            ->where('type', $type)
            ->find($modelId);
    }

    /**
     * @param int $id
     * @param SpeakerTypesEnum $type
     * @return bool
     */
    public function delete(int $id, SpeakerTypesEnum $type): bool
    {
        return QueryBuilder::for($this->model)
            ->where([
                'type' => $type
            ])
            ->withTrashed()
            ->findOrFail($id)
            ->forceDelete();
    }

    /**
     * @param int $id
     * @param SpeakerTypesEnum $type
     * @return bool|mixed|null
     */
    public function archive(int $id, SpeakerTypesEnum $type): mixed
    {
        return QueryBuilder::for($this->model)
            ->where([
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
        if (array_key_exists('name', $Data)) {
            $Data['name'] = json_encode($Data['name']);
        }
        if (array_key_exists('description', $Data)) {
            $Data['description'] = json_encode($Data['description']);
        }
    }
}
