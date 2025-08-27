<?php

namespace Admin\Classic\Http\Resources\ClassicUserApplication;

use Admin\Http\Resources\BaseCollection;

class ClassicUserApplicationCollection extends BaseCollection
{
    protected string $type = 'classic-user-application';
    protected string $namespace = 'Admin.';

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
