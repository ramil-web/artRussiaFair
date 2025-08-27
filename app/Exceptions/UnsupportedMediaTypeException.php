<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class UnsupportedMediaTypeException extends Exception implements JsonApiException
{
    public function __construct()
    {
        $this->message = 'Запроса имеет заголовок Content-Type, который сервер не поддерживает.';
        $this->code = Response::HTTP_UNSUPPORTED_MEDIA_TYPE;

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
