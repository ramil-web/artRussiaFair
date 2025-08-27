<?php

namespace Admin\Http\Resources\Gallery;

use Admin\Http\Resources\BaseCollection;

class GalleryCollection extends BaseCollection
{
    protected string $type = 'gallery';
    protected string $namespace = 'Admin.';

    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {

        return [
            'data' => $this->collection,
        ];
    }

}
