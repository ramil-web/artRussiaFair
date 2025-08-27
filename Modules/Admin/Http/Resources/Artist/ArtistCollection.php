<?php

namespace Admin\Http\Resources\Artist;

use Admin\Http\Resources\BaseCollection;

class ArtistCollection extends BaseCollection
{

    protected string $type = 'artist';
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
