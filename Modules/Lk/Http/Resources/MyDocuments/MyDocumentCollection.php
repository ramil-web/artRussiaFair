<?php

namespace Lk\Http\Resources\MyDocuments;

use Lk\Http\Resources\BaseCollection;

class MyDocumentCollection extends BaseCollection
{

    protected string $type = 'my-documents';
    protected string $namespace = 'lk.';

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
