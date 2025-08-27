<?php

namespace App\Exceptions;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class ImageUploadException extends InvalidArgumentException implements JsonApiException
{
    public function __construct($message = '')
    {
        $this->message = $message ?: 'Изображение должно быть закодировано в Base64.';
        $this->code = Response::HTTP_UNPROCESSABLE_ENTITY;

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
