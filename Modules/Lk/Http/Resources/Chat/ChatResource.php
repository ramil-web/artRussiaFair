<?php

namespace Lk\Http\Resources\Chat;

use Lk\Http\Resources\BaseResource;

class ChatResource extends BaseResource
{
    protected array $relationships = [
        'user'
    ];
    protected string $type = 'chat';
    protected string $namespace='lk.';
}
