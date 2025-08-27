<?php

namespace Admin\Http\Resources\PartnerCategory;

use App\Http\Resources\BaseCollection;

class PartnerCategoryCollection extends BaseCollection
{
    protected string $type = 'partner-category';
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
