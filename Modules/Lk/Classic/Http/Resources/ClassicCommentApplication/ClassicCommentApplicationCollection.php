<?php

namespace Lk\Classic\Http\Resources\ClassicCommentApplication;

use Lk\Http\Resources\BaseCollection;

class ClassicCommentApplicationCollection extends BaseCollection
{
    protected string $type = 'classic-app-comments';
    protected string $namespace = 'lk.';

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }

}
