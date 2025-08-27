<?php

namespace Admin\Http\Resources\UserApplications;

use Admin\Http\Resources\BaseCollection;


/** @see \App\Models\UserApplication */
class UserApplicationCollection extends BaseCollection
{
    protected string $type = 'user-applications';
    protected string $namespace = 'Admin.';

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,

        ];
    }
}
