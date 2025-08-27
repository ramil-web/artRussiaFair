<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\TimeSlot\GetTimeSlotRequest;
use Admin\Http\Requests\TimeSlot\TimeSlotRequest;
use Admin\Http\Requests\TimeSlot\TimeSlotUpdateRequest;
use Admin\Repositories\TimeSlot\TimeSlotRepository;
use Admin\Services\TimeSlotService;
use App\Exceptions\CustomException;
use App\Exports\TimeSlotExport;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use Maatwebsite\Excel\Facades\Excel;
use OpenApi\Annotations as OA;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class TimeSlotController extends Controller
{
    public function __construct(
        public TimeSlotService    $timeSlotService,
        public TimeSlotRepository $timeSlotRepository)
    {
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/slot/timeslot",
     *      operationId="createSlot",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Слоты"},
     *      summary="Создание слотов",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            required={"begin","end","action","interval"},
     *            @OA\Property(property="begin",description="Начальная дата и время",type="string",example="2023-11-03 15:00"),
     *            @OA\Property(property="end",description="конечная дата и время",type="string",example="2023-11-03 16:00"),
     *            @OA\Property(property="interval", description="Интервал", type="integer", example=30),
     *            @OA\Property(property="action",description="Слоты для заезда и выезда",type="string",example="check_in"),
     *            @OA\Property(property="event_id", description="Порядковый номер события",type="integer",example="1"),
     *             ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *              @OA\Schema(
     *               type="object",
     *               @OA\Property(property="data", description="ID категории", type="array",
     *               @OA\Items(
     *               @OA\Property(
     *                   property="id",
     *                   description="Порядковый номер слота",
     *                   type="integer",
     *                   example="1"
     *               ),
     *               @OA\Property(
     *                   property="date",
     *                   description="Дата",
     *                   type="string",
     *                   example="2023-11-03"
     *               ),
     *              @OA\Property(
     *                   property="interval_times",
     *                   description="Интервал времени, начало",
     *                   type="string",
     *                   example="16:00:00"
     *               ),
     *             @OA\Property(
     *                   property="count",
     *                   description="Количество участников слота",
     *                   type="integer",
     *                   example="1"
     *                 ),
     *            @OA\Property(
     *                   property="status",
     *                   description="Статус слота",
     *                   type="boolean",
     *                   example="true"
     *                 ),
     *             @OA\Property(
     *                   property="action",
     *                   description="Тип слота заезд/выезд",
     *                   type="string",
     *                   example="check_in"
     *               ),
     *            @OA\Property(
     *                   property="event_id",
     *                   description="Идентификатор привязанного событие",
     *                   type="integer",
     *                   example="1"
     *                 ),
     *               ),
     *               ),
     *               @OA\Property(
     *                  property="metadata",
     *                  type="object",
     *                  @OA\Property(property="message", example="Ok"),
     *                ),
     *             ),
     *          )
     *      ),
     *      @OA\Response(response=401,description="Unauthenticated"),
     *      @OA\Response(response=400,description="Bad Request"),
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
     *      @OA\Response(response=500,description="Server error, the slot has not been created")
     * )
     * @param TimeSlotRequest $timeSlotRequest
     * @return ApiSuccessResponse
     * @throws Throwable
     */
    public function index(TimeSlotRequest $timeSlotRequest): ApiSuccessResponse
    {
        $dataApp = $timeSlotRequest->validated();
        $response = $this->timeSlotService->updateOrCreate($dataApp);
        return new ApiSuccessResponse($response, ['message' => 'Ok'], ResponseAlias::HTTP_OK);
    }


    /**
     * @OA\Get(
     *      path="/api/v1/admin/slot/interval",
     *      operationId="getInterval",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Слоты"},
     *      summary="Получение интервал слотов по ID событие",
     *     @OA\Parameter(
     *         name="event_id",
     *         in="query",
     *         required=true,
     *         description="ID событие",
     *         @OA\Schema(
     *            type="string"
     *       ),
     *       example=1,
     *
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *              @OA\Schema(
     *                 @OA\Property(
     *                    property="data",
     *                    description="Интервалы слотов",
     *                    type="object",
     *                    @OA\Property(property="check_in", type="object",
     *                        @OA\Property(property="begin", description="Начало", type="string",example="2023-11-02 15:00:00"),
     *                        @OA\Property(property="end", description="Начало", type="string",example="2023-11-03 15:00:00"),
     *                    ),
     *                   @OA\Property(property="exit", type="object",
     *                         @OA\Property(property="begin", description="Начало", type="string",example="2023-11-02 15:00:00"),
     *                         @OA\Property(property="end", description="Начало", type="string",example="2023-11-03 15:00:00"),
     *                     ),
     *                    ),
     *                @OA\Property(property="metadata",type="object",
     *                     @OA\Property(property="message", example="Ok"),
     *                   ),
     *                  ),
     *               ),
     *          ),
     *      @OA\Response(response=401,description="Unauthenticated"),
     *      @OA\Response(response=400,description="Bad Request"),
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
     * @param GetTimeSlotRequest $getTimeSlotRequest
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function get(GetTimeSlotRequest $getTimeSlotRequest): ApiSuccessResponse
    {
        $dataApp = $getTimeSlotRequest->validated();
       return new ApiSuccessResponse(
           $this->timeSlotService->getTimeSlotIntervals($dataApp['event_id']),
           ['message' => "Ok"],
           ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/slot/export",
     *      operationId="exportTimeSlot",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Слоты"},
     *      summary="Экспорт слотов",
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *              @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                  property="data",
     *                  type="string",
     *                  example="C:\\xampp\\htdocs\\newapiartrussiafair\\storage\\/app//time-slots/time-slots-1701685838.xlsx"
     *                 ),
     *               @OA\Property(property="metadata", type="object",
     *                @OA\Property(property="message", type="string", example="Ok"),
     *               ),
     *              ),
     *            ),
     *          ),
     *     @OA\Response(response=401,description="Unauthenticated"),
     *     @OA\Response(response=400,description="Bad Request"),
     *     @OA\Response(response=404,description="not found"),
     *     @OA\Response(response=403,description="Forbidden",
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
     * @param TimeSlotExport $slotExport
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function export(TimeSlotExport $slotExport): ApiSuccessResponse
    {
        try {
            $fileName = '/time-slots/time-slots-' . date('Y-m-d_H-i-s') . '.xlsx';
            Excel::store($slotExport, $fileName);
            $link = storage_path('/app/' . $fileName);
            return new ApiSuccessResponse($link, ['message' => 'Ok'], ResponseAlias::HTTP_OK);
        } catch (Exception|\PhpOffice\PhpSpreadsheet\Exception $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/admin/slot/interval/update",
     *     operationId="updateSlotInterval",
     *     security={{"bearerAuth":{}}},
     *     tags={"Admin|Слоты"},
     *     summary="Редактирование интервала слотов по ID событие",
     *     @OA\Parameter(
     *                name="event_id",
     *                in="query",
     *                description="ID событие",
     *                @OA\Schema(
     *                   type="integer",
     *                   example=1
     *               )
     *           ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            required={"begin","end","action","interval"},
     *            @OA\Property(property="check_in", type="object",
     *                   @OA\Property(property="begin",description="Начальная дата и время",type="string",example="2023-11-03 15:00"),
     *                   @OA\Property(property="end",description="конечная дата и время",type="string",example="2023-11-03 16:00"),
     *            ),
     *            @OA\Property(property="exit", type="object",
     *                   @OA\Property(property="begin",description="Начальная дата и время",type="string",example="2023-11-03 15:00"),
     *                  @OA\Property(property="end",description="конечная дата и время",type="string",example="2023-11-03 16:00"),
     *             ),
     *             ),
     *          ),
     *      ),
     *      @OA\Response(
     *           response=200,
     *           description="Success",
     *           @OA\MediaType(
     *               mediaType="application/vnd.api+json",
     *               @OA\Schema(
     *                  @OA\Property(
     *                     property="data",
     *                     description="Интервалы слотов",
     *                     type="object",
     *                     @OA\Property(property="check_in", type="object",
     *                         @OA\Property(property="begin", description="Начало", type="string",example="2023-11-02 15:00:00"),
     *                         @OA\Property(property="end", description="Начало", type="string",example="2023-11-03 15:00:00"),
     *                     ),
     *                    @OA\Property(property="exit", type="object",
     *                          @OA\Property(property="begin", description="Начало", type="string",example="2023-11-02 15:00:00"),
     *                          @OA\Property(property="end", description="Начало", type="string",example="2023-11-03 15:00:00"),
     *                      ),
     *                     ),
     *                 @OA\Property(property="metadata",type="object",
     *                      @OA\Property(property="message", example="Ok"),
     *                    ),
     *                   ),
     *                ),
     *           ),
     *      @OA\Response(response=401,description="Unauthenticated"),
     *      @OA\Response(response=400,description="Bad Request"),
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
     *      @OA\Response(response=500,description="Server error, the slot has not been created")
     * )
     * @param TimeSlotUpdateRequest $timeSlotRequest
     * @return ApiSuccessResponse
     * @throws Throwable
     */
    public function update(TimeSlotUpdateRequest $timeSlotRequest): ApiSuccessResponse
    {
        $dataApp = $timeSlotRequest->validated();
        $response = $this->timeSlotService->update($dataApp);
        return new ApiSuccessResponse($response, ['message' => 'Ok'], ResponseAlias::HTTP_OK);
    }
}
