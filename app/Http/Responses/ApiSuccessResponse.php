<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class ApiSuccessResponse implements Responsable

{

    /**
     * @param mixed $data
     * @param array $metadata
     * @param int $code
     * @param array $headers
     */

    public function __construct(
        private mixed $data,
        private array $metadata,
        private int   $code = ResponseAlias::HTTP_OK,
        private array $headers = []
    )
    {
    }


    /**
     * @param  $request
     * @return JsonResponse
     */

    public function toResponse($request)
    {

        return response()->json(

            [
                'data' => $this->data,
                'metadata' => $this->metadata,
            ],
            $this->code,
        )->header('Content-Type', 'application/vnd.api+json', true);
    }

}
