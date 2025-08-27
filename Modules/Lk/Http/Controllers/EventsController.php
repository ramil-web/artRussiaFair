<?php

namespace Lk\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use Lk\Http\Requests\Event\SearchEventRequest;
use Lk\Http\Requests\Event\ShowEventSlotsRequest;
use Lk\Services\EventService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EventsController extends Controller
{
    public function __construct(protected EventService $eventService)
    {
    }

    /**
     * @OA\Get(
     *    path="/api/v1/lk/event",
     *    operationId="Lk.getActiveEvent",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|События"},
     *    summary="Получает подходяшую, активную событию",
     *    @OA\Parameter(
     *        name="category",
     *        in="query",
     *        required=true,
     *        description="Категория событие",
     *        @OA\Schema(
     *            type="string",
     *       ),
     *    ),
     *    @OA\Response(
     *       response=200,
     *       description="Success",
     *       @OA\MediaType(
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
    public function searchEvent(SearchEventRequest $request): ApiSuccessResponse
    {
        /**
         * This type of temporary is static, then you may have to get it from the front
         */
        $type = 'main';
        $appData = $request->validated();
        return new ApiSuccessResponse(
            $this->eventService->searchEvent($type,  $appData['category']),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *      path="/api/v1/lk/event/slots",
     *      operationId="LkEventSlots",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|События"},
     *      summary="Просмотр слотов привязанных событию (выставки)",
     *      @OA\Parameter(
     *        name="id",
     *        in="query",
     *        required=true,
     *        description="ID события",
     *        @OA\Schema(
     *           type="string",
     *      ),
     *   ),
     *  @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
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
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     * @param ShowEventSlotsRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function slots(ShowEventSlotsRequest $request): ApiSuccessResponse
    {
        $dataApp = $request->validated();
        return new ApiSuccessResponse(
            $this->eventService->slots($dataApp['id']),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }
}
