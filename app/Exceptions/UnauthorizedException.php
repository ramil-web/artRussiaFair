<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
//use Synergy\Admin\Exceptions\JsonApiException;
use Throwable;

class UnauthorizedException extends Exception implements JsonApiException
{
    public function __construct(Throwable $e)
    {
        $this->message = __($e->getMessage());
        $this->code = $e->getMessage() === 'Вы не авторизованы.' ? Response::HTTP_UNAUTHORIZED : Response::HTTP_FORBIDDEN;

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
