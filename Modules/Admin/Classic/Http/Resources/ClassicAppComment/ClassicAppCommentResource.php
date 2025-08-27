<?php

namespace Admin\Classic\Http\Resources\ClassicAppComment;

use Admin\Http\Resources\BaseResource;

class ClassicAppCommentResource extends BaseResource
{
    protected array $relationships = [
        'user',
        'classicUserApp'
    ];
    protected string $type = 'classic-app-comments';
    protected string $namespace='Admin.';
}
