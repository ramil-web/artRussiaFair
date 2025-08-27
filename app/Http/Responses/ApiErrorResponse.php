<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class ApiErrorResponse implements Responsable

{
    public function __construct(
        private string     $message,
        private ?Throwable $exception = null,
        private int        $code = Response::HTTP_INTERNAL_SERVER_ERROR,
        private array      $headers = []
    )
    {
    }


    /**
     * @param  $request
     * @return JsonResponse
     */

    public function toResponse($request): JsonResponse
    {
        $response = ['message' => $this->message];

        if (!is_null($this->exception) && config('app.debug')) {
            $response['debug'] = [
                'message' => $this->exception->getMessage(),
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
            ];
        }
        return response()->json($response, $this->code)->header('Content-Type', 'application/vnd.api+json', true);
    }

}
