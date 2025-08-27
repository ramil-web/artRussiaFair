<?php /** @noinspection UnsupportedStringOffsetOperationsInspection */

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ResourceValidationException extends Exception implements JsonApiException
{
    protected object $exception;

    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
        $this->code = Response::HTTP_UNPROCESSABLE_ENTITY;

        parent::__construct($this->message, $this->code);
    }

    public function makeResponseData(): array
    {
        $response = [
            'status' => (string)$this->getCode(),
            'message' => 'Ошибка заполнения',
            'errors' =>[]
        ];

        foreach ($this->exception->errors() as $source => $error) {

            $response['errors'][] = [
                'detail' => array_shift($error),
                'source' => str_replace('.', '/', $source)
            ];
        }

        return $response;
    }
}
