<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class NotFoundHttpException extends Exception implements JsonApiException
{
    public function __construct(Throwable $e)
    {
        $this->message = sprintf(
            __('The route %s could not be found.'),
            request()->path()
        );

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
