<?php

namespace Admin\Classic\Http\Resources\ClassicAppComment;

use Admin\Http\Resources\BaseCollection;

class ClassicAppCommentCollection extends BaseCollection
{
    protected string $type = 'classic-app-comment';
    protected string $namespace = 'Admin.';

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
