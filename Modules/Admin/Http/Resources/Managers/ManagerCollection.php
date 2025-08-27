<?php

namespace Admin\Http\Resources\Managers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ManagerCollection extends ResourceCollection
{
    protected string $type = 'manager';
    protected string $namespace = 'Admin.';

    public function toArray($request)
    {
        return [
            'data' => $this->collection,

        ];
    }

}
