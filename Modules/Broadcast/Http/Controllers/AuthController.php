<?php

namespace Broadcast\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Broadcast\Http\Requests\BroadcastLoginRequest;
use Broadcast\Services\BroadcastService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    public function __construct(public BroadcastService $service)
    {
    }

    /**
     * @OA\Post(
     *      path="/api/v1/broadcast/auth/login",
     *      tags={"Broadcasts|Auth"},
     *      summary="Login",
     *      operationId="BroadcastLogin",
     *
     *     @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *          @OA\Property(property="barcode", type="string", example="6052666350310"),
     *        )
     *      )
     *     ),
     *  @OA\Response(
     *     response=200,
     *     description="Success",
     *       @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                 property="data",
     *                 description="ID Трансляции",
     *                 type="object",
     *                 @OA\Property(property="id", example="1", type="integer"),
     *                 @OA\Property(property="barcode", example="605266635031", type="integer"),
     *                 @OA\Property(property="product_id", example="6052666as31", type="integer"),
     *              ),
     *             @OA\Property(
     *                 property="metadata",
     *                 type="object",
     *                 @OA\Property(property="message", type="text", example="Auth success"),
     *             )
     *          ),
     *      )
     *   ),
     * @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     * @OA\Response(
     *    response=403,
     *    description="Forbidden",
     *    @OA\JsonContent(
     *             @OA\Property(property="message", type="object", example="Access is denied!"),
     *        ),
     *      ),
     *   ),
     *)
     * @param BroadcastLoginRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse|JsonResponse
     * @throws CustomException
     */

    public function login(BroadcastLoginRequest $request): ApiSuccessResponse|ApiErrorResponse|JsonResponse
    {
        $appData = $request->validated();
        $response = $this->service->logIn($appData['barcode']);
        if (!$response) {
            return new ApiErrorResponse(
                'Access is denied!',
                null,
                ResponseAlias::HTTP_FORBIDDEN

            );
        } else {
            return new ApiSuccessResponse(
                $response,
                ['message' => 'Auth success'],
                ResponseAlias::HTTP_OK
            );
        }
    }
}
