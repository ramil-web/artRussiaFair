<?php

namespace Lk\Http\Resources\CommentApplication;

use Lk\Http\Resources\BaseCollection;

/** @see \App\Models\AppComment */
class CommentApplicationCollection extends BaseCollection
{
    protected string $type = 'app-comments';
    protected string $namespace = 'lk.';

    public function toArray($request)
    {
        return [
            'data' => $this->collection,
        ];
    }
}
