<?php

namespace Admin\Services\Relation;

use Admin\Repositories\Relation\RelationRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RelationService
{
    protected string $table;

    protected array $data;

    protected Model $model;

    private RelationRepository $relationRepository;

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    public function __construct(RelationRepository $relationRepository)
    {
        $this->relationRepository = $relationRepository;
    }

    public function sync(array $data, string $entity, int $id, string $relation)
    {
        $this->getModelByEntity($entity);

        $model = $this->getModel()->with($relation)->findOrFail($id);

        if ($model->{$relation} instanceof Collection) {
            $this->relationRepository->sync($model, $relation, array_column($data, 'id'));
        } else {
            $this->relationRepository->update($model, ["{$relation}_id" => $data['id']]);
        }

        $this->relationRepository->refresh($model);

        return $model->{$relation};
    }

    public function attach(array $data, string $entity, int $id, string $relation)
    {
        $this->getModelByEntity($entity);

        $model = $this->getModel()->with($relation)->findOrFail($id);

        $existsIds = array_column($model->{$relation}->toArray(), 'id');
        $ids = array_diff(array_column($data, 'id'), $existsIds);

        $this->relationRepository->attach($model, $relation, $ids);

        $this->relationRepository->refresh($model);

        return $model->{$relation};
    }

    public function detach(array $data, string $entity, int $id, string $relation)
    {
        $this->getModelByEntity($entity);

        $model = $this->getModel()->with($relation)->findOrFail($id);

        $this->relationRepository->detach($model, $relation, array_column($data, 'id'));

        $this->relationRepository->refresh($model);

        return $model->{$relation};
    }

    public function relations(string $entity, int $id, string $relation): Model|Collection
    {
        $this->getModelByEntity($entity);
//dd($relation,$entity);
        $model = $this->getModel()->with($relation)->findOrFail($id);

        return $model->{$relation};
    }

    /**
     * Get table name by relation.
     *
     * @param string $relation
     * @return void
     */
    public function getTableNameByRelation(string $relation): void
    {
        $this->setTable($this->getModelPath($relation)::MODEL_TYPE);
    }

    /**
     * Array is multidimensional.
     *
     * @param array $array
     * @return bool
     */
    public function isArrayMultidimensional(array $array): bool
    {
        return count($array) !== count($array, COUNT_RECURSIVE);
    }

    /**
     * Get model name by entity.
     *
     * @param string $entity
     * @return string
     */
    public function getModelName(string $entity): string
    {
        return Str::studly(Str::singular($entity));
    }

    /**
     * Get model by entity.
     *
     * @param string $entity
     * @return void
     */
    private function getModelByEntity(string $entity): void
    {
//        dd($entity);
        $modelPath = $this->getModelPath($entity);

        $this->setModel((new $modelPath));
    }

    /**
     * Get model path by entity.
     *
     * @param string $entity
     * @return string
     */
    private function getModelPath(string $entity): string
    {
        return "App\\Models\\{$this->getModelName($entity)}";
    }
}
