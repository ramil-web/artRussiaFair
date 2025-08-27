<?php

namespace Lk\Classic\Http\Resources\ClassicCommentApplication;

use Lk\Http\Resources\BaseResource;

class ClassicCommentApplicationResource extends BaseResource
{
    protected array $relationships = [
        'user',
        'classicUserApp'
    ];
    protected string $type = 'classic-app-comments';
    protected string $namespace='lk.';
}
