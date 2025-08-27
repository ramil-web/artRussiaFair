<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

;

class DeleteResponse implements Responsable
{
    public function __construct(
        private string    $message,
    )
    {
    }

    /**
     * @param  $request
     * @return JsonResponse
     */

    public function toResponse($request): JsonResponse
    {
        $message = [
            'data' => true,
            'metadata' => [
                'message' => $this->message
            ]
        ];
        return response()->json($message, Response::HTTP_OK)->header('Content-Type', 'application/vnd.api+json', true);
    }
}
