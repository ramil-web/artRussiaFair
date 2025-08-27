<?php

namespace Admin\Http\Resources\Artist;

use Admin\Http\Resources\BaseResource;

class ArtistResource extends BaseResource
{
    protected array $relationships = [
        'events',
    ];
    protected string $type = 'artist';
    protected string $namespace='Admin.';
}
