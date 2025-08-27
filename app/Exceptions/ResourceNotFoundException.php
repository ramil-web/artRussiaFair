<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ResourceNotFoundException extends Exception implements JsonApiException
{
    public function __construct(ModelNotFoundException $exception)
    {
        $id = $exception->getIds();

        $this->message = sprintf("Ресурс с идентификатором %s не был найден.", array_shift($id));
        $this->code = Response::HTTP_NOT_FOUND;

        parent::__construct($this->message, $this->code);
    }

    public function makeResponseData(): array
    {
        return [
            'errors' => [
                [
                    'status' => (string)$this->getCode(),
                    'detail' => $this->getMessage(),
                ],
            ],
        ];
    }
}
