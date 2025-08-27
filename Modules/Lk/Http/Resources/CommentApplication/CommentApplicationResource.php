<?php

namespace Lk\Http\Resources\CommentApplication;

use Lk\Http\Resources\BaseResource;

class CommentApplicationResource extends BaseResource
{
    protected array $relationships = [
        'user',
        'user_app'
    ];
    protected string $type = 'app-comments';
    protected string $namespace='lk.';
}
