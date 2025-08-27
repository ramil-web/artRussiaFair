<?php

namespace Admin\Http\Resources\MyDocuments;

use Lk\Http\Resources\BaseCollection;

class MyDocumentCollection extends BaseCollection
{

    protected string $type = 'my-documents';
    protected string $namespace = 'Admin.';

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
