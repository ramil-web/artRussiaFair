<?php

namespace Admin\Http\Resources\Chat;

use Admin\Http\Resources\BaseResource;

class ChatResource extends BaseResource
{
    protected array $relationships = [];
    protected string $type = 'chat';
    protected string $namespace = 'Admin.';
}
