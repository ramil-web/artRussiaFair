<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface BaseRepositoriesInterface
{
    public function getAllByFilters(
        array $role = [],
        array $withRelation =[],
        array $allowedFilters = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        array $allowedSorts =[],
        bool $withTrashed = false,
        int $perPage = null
    );

    public function create(array $Data): ?Model;

    public function update(Model $model, array $Data): ?bool;
    public function findById(
        int $modelId,
        array $withRelation =[],
        array $allowedFields = [],
        array $allowedIncludes = [],
        bool $withTrashed = false
    ): ?Model;
    public function getSelf(): ?Model;

    public function updateSelf(): ?Model;

    public function softDelete(Model $model): bool;

    public function restore(Model $model): bool;

    public function forceDelete(Model $model): bool;


}
