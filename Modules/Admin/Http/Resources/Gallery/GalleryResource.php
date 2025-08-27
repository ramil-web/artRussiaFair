<?php

namespace Admin\Http\Resources\Gallery;

use Admin\Http\Resources\BaseResource;

class GalleryResource extends BaseResource
{

    protected array $relationships = [
        'events',
    ];
    protected string $type = 'gallery';
    protected string $namespace='Admin.';
}
