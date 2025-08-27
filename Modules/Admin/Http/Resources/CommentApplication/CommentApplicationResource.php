<?php

namespace Admin\Http\Resources\CommentApplication;

use Admin\Http\Resources\BaseResource;

class CommentApplicationResource extends BaseResource
{
    protected array $relationships = [
        'user',
        'user_app'
    ];
    protected string $type = 'app-comments';
    protected string $namespace='Admin.';
}
