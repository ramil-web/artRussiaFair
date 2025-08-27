<?php

namespace Lk\Repositories\Relation;

use Illuminate\Database\Eloquent\Model;

interface RelationRepositoryInterface
{
    public function sync(Model $model, string $relation, array $ids): void;

    public function attach(Model $model, string $relation, array $ids): void;

    public function detach(Model $model, string $relation, array $ids): void;

    public function refresh(Model $model): void;

    public function update(Model $model, array $data): void;
}
