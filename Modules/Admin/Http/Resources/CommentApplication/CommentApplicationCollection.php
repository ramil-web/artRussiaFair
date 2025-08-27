<?php

namespace Admin\Http\Resources\CommentApplication;

use Lk\Http\Resources\BaseCollection;

/** @see \App\Models\AppComment */
class CommentApplicationCollection extends BaseCollection
{
    protected string $type = 'visualization-comment';
    protected string $namespace = 'Admin.';

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
