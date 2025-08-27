<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class NotAcceptableException extends Exception implements JsonApiException
{
    public function __construct()
    {
        $this->message = 'Запрос имеет заголовок Accept, который сервер не поддерживает.';
        $this->code = Response::HTTP_NOT_ACCEPTABLE;

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
