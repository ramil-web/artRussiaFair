<?php

namespace Lk\Classic\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use Lk\Classic\Services\ClassicEventService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ClassicEventController extends Controller
{
public function __construct(public ClassicEventService $service)
{
}

    /**
     * @OA\Get(
     *      path="/api/v1/lk/classic/event",
     *      operationId="LkClassicActiveEvent",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Classic|События"},
     *      summary="Получает подходяшую, активную событию",
     *      @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *            mediaType="application/vnd.api+json",
     *        ),
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=400,description="Bad Request"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=403,description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="array",
     *             @OA\Items(
     *                 @OA\Property(property="status", example="403"),
     *                 @OA\Property(property="detail", example="User does not have the right roles.")
     *             ),
     *          ),
     *        ),
     *     ),
     * ),
     *
     * @throws CustomException
     */
    public function searchEvent(): ApiSuccessResponse
    {
        /**
         * This type of temporary is static, then you may have to get it from the front
         */
        $type = 'main';
        return new ApiSuccessResponse(
            $this->service->searchEvent($type),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }
}
