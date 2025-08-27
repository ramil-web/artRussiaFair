<?php

namespace Lk\Http\Resources\MyDocuments;

use Lk\Http\Resources\BaseResource;

class MyDocumentResource extends BaseResource
{
    protected array $relationships = [
        'contacts', 'requisites'
    ];
    protected string $type = 'my-documents';
    protected string $namespace='lk.';
}
