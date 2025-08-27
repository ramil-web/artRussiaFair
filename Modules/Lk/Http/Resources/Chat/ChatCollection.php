<?php

namespace Lk\Http\Resources\Chat;

use Lk\Http\Resources\BaseCollection;

class ChatCollection extends BaseCollection
{
    protected string $namespace = 'lk.';

    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
