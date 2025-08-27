<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ResponseErrorException extends Exception implements JsonApiException
{
    private bool $isProduction;
    public function __construct(Throwable $e)
    {
        $this->isProduction = env('APP_ENV', 'develop') === 'production';
        $this->message = $this->isProduction ? 'Ошибка на стороне сервера.' : $e->getMessage();
        $this->code = Response::HTTP_INTERNAL_SERVER_ERROR;
        $this->file = $e->getFile();
        $this->line = $e->getLine();

        parent::__construct($this->message, $this->code);
    }

    public function makeResponseData(): array
    {
        $data = [
            'status' => (string)$this->getCode(),
            'detail' => $this->getMessage()
        ];
        if (!$this->isProduction) {
            $data['file'] = $this->getFile();
            $data['line'] = (string)$this->getLine();
        }

        return [
            'errors' => [$data]
        ];
    }
}
