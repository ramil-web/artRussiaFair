<?php

namespace App\Repositories\Relation;

use Illuminate\Database\Eloquent\Model;

class RelationRepository implements RelationRepositoryInterface
{
    public function sync(Model $model, string $relation, array $ids): void
    {
        $model->{$relation}()->sync($ids);
    }

    public function attach(Model $model, string $relation, array $ids): void
    {
        $model->{$relation}()->attach($ids);
    }

    public function detach(Model $model, string $relation, array $ids): void
    {
        $model->{$relation}()->detach($ids);
    }

    public function refresh(Model $model): void
    {
        $model->refresh();
    }

    public function update(Model $model, array $data): void
    {
        $model->update($data);
    }
}
