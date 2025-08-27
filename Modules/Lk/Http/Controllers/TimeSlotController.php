<?php

namespace Lk\Http\Controllers;

use App\Enums\TimeSlotEnum;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use Lk\Http\Requests\TimeSlot\TimeSlotListRequest;
use Lk\Repositories\TimeSlot\TimeSlotRepository;
use Lk\Services\TimeSlotService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TimeSlotController extends Controller
{



    public function __construct(
        public TimeSlotService $timeSlotService,
        public TimeSlotRepository $timeSlotRepository
    ) {}


    /**
     * @OA\Get(
     *      path="/api/v1/lk/slot/getlots/check_in",
     *      operationId="lkgetSlotCheckIn",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Слоты"},
     *      summary="Список всех доступных слотов для заезда",
     *     @OA\Parameter(
     *         name="filter[id]",
     *         in="query",
     *         description="Фильтр по ID",
     *         @OA\Schema(
     *               type="integer",
     *             )
     *     ),
     *    @OA\Parameter(
     *         name="filter[date]",
     *         in="query",
     *         description="Фильтр по дате ",
     *         @OA\Schema(
     *               type="string",
     *             )
     *     ),
     *    @OA\Parameter(
     *         name="filter[event_id]",
     *         in="query",
     *         description="Фильтр по ID событий",
     *         @OA\Schema(
     *               type="integer",
     *             )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *              @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                   @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="date", type="sting", example="2023-11-03"),
     *                  @OA\Property(property="interval_times", type="sting", example="17:15:00"),
     *                  @OA\Property(property="action", type="sting", example="check_in"),
     *                  @OA\Property(property="event_id", type="integer", example="1"),
     *                  ),
     *                 ),
     *               @OA\Property(property="metadata", type="object",
     *                @OA\Property(property="message", type="string", example="Ok"),
     *               ),
     *              ),
     *            ),
     *          ),
     *      @OA\Response(response=401,description="Unauthenticated"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404,description="not found"),
     *      @OA\Response(response=403,description="Forbidden",
     *          @OA\JsonContent(
     *             @OA\Property(property="errors", type="array",
     *                 @OA\Items(
     *                    @OA\Property(property="status", example="403"),
     *                    @OA\Property(property="detail", example="User does not have the right roles.")
     *                 ),
     *             ),
     *          ),
     *      ),
     *     @OA\Response(response=500,description="Server error")
     * )
     * @param TimeSlotListRequest $timeSlotListRequest
     * @return ApiSuccessResponse
     */
    public function getCheckInSlots(TimeSlotListRequest $timeSlotListRequest): ApiSuccessResponse
    {
        $dataApp = $timeSlotListRequest->validated();
        $type = TimeSlotEnum::CHECK_IN();
        $data = $this->timeSlotService->getSlots($dataApp, $type);
        return new ApiSuccessResponse($data, ['message' => 'Ok'], ResponseAlias::HTTP_OK);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/lk/slot/getlots/exit",
     *      operationId="lkgetSlotExit",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Слоты"},
     *      summary="Список всех доступных слотов для выезда",
     *     @OA\Parameter(
     *         name="filter[id]",
     *         in="query",
     *         description="Фильтр по ID",
     *         @OA\Schema(
     *               type="integer",
     *             )
     *     ),
     *    @OA\Parameter(
     *         name="filter[date]",
     *         in="query",
     *         description="Фильтр по дате ",
     *         @OA\Schema(
     *               type="string",
     *             )
     *     ),
     *    @OA\Parameter(
     *         name="filter[event_id]",
     *         in="query",
     *         description="Фильтр по ID событий",
     *         @OA\Schema(
     *               type="integer",
     *             )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *              @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                   @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="date", type="sting", example="2023-11-03"),
     *                  @OA\Property(property="interval_times", type="sting", example="17:15:00"),
     *                  @OA\Property(property="action", type="sting", example="exit"),
     *                  @OA\Property(property="event_id", type="integer", example="1"),
     *                  ),
     *                 ),
     *                @OA\Property(property="metadata", type="object",
     *                @OA\Property(property="message", type="string", example="Ok"),
     *               ),
     *              ),
     *            ),
     *          ),
     *      @OA\Response(response=401,description="Unauthenticated"),
     *      @OA\Response(response=400,description="Bad Request"),
     *      @OA\Response(response=404, description="not found"),
     *      @OA\Response(response=403,description="Forbidden",
     *          @OA\JsonContent(
     *             @OA\Property(property="errors", type="array",
     *                 @OA\Items(
     *                    @OA\Property(property="status", example="403"),
     *                    @OA\Property(property="detail", example="User does not have the right roles.")
     *                 ),
     *             ),
     *          ),
     *      ),
     *     @OA\Response(response=500,description="Server error")
     * )
     * @param TimeSlotListRequest $timeSlotListRequest
     * @return ApiSuccessResponse
     */
    public function getExitSlots(TimeSlotListRequest $timeSlotListRequest): ApiSuccessResponse
    {
        $dataApp = $timeSlotListRequest->validated();
        $type = TimeSlotEnum::EXIT();
        $data = $this->timeSlotService->getSlots($dataApp, $type);
        return new ApiSuccessResponse($data, ['message' => 'Ok'], ResponseAlias::HTTP_OK);
    }
}
