<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Requests\Event\EventListRequest;
use App\Http\Requests\Event\ShowEventRequest;
use App\Http\Resources\Event\EventResource;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\EventService;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EventsController extends Controller
{

    private EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * @OA\Get(
     *    path="/api/v1/event/list",
     *    operationId="GetEvents",
     *    security={{"bearerAuth":{}}},
     *    tags={"App|События"},
     *    summary="Список событытий",
     *    @OA\Parameter(
     *       name="filter[category]",
     *       in="query",
     *       description="Категория события",
     *       required=false,
     *       @OA\Schema(
     *          type="string",
     *         )
     *     ),
     *    @OA\Parameter(
     *       name="year",
     *       in="query",
     *       required=false,
     *       @OA\Schema(
     *          type="integer",
     *          example=2024,
     *       )
     *    ),
     *    @OA\Parameter(
     *       name="has_partners",
     *       in="query",
     *       description="Фильтр по наличию привязки к партнерам",
     *       @OA\Schema(
     *          type="string",
     *          example="has_partners"
     *             )
     *    ),
     *    @OA\Parameter(
     *        name="partner_category_id",
     *        in="query",
     *        description="Фильтр по наличию привязки к категории партнера",
     *        @OA\Schema(
     *          type="integer",
     *          example=1,
     *         )
     *     ),
     *    @OA\Response(
     *      response=200,
     *      description="Success",
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
     *
     * @throws CustomException
     */
    public function list(EventListRequest $request): ApiSuccessResponse
    {
        $appData = $request->validated();
        return new ApiSuccessResponse(
            $this->eventService->list($appData),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
    }

    /**
     * @OA\Get(
     *    path="/api/v1/events",
     *    operationId="GetEventId",
     *    security={{"bearerAuth":{}}},
     *    tags={"App|События"},
     *    summary="Просмотр события",
     *    @OA\Parameter(
     *        name="id",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     * @OA\Response(
     *      response=200,
     *      description="Success",
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
     *
     * @throws CustomException
     */
    public function show(ShowEventRequest $request): ApiSuccessResponse
    {
        $dataApp = $request->validated();
        return new ApiSuccessResponse(
            new  EventResource($this->eventService->show($dataApp['id'])),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }
}
