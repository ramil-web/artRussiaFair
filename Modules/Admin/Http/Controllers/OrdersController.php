<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\Order\ExportOrdersRequest;
use Admin\Http\Requests\Order\OrderRequest;
use Admin\Http\Requests\Order\UpdateOrderRequest;
use Admin\Http\Resources\Order\OrderCollection;
use Admin\Http\Resources\Order\OrderResource;
use Admin\Services\CommonService;
use Admin\Services\OrderService;
use App\Exceptions\CustomException;
use App\Exceptions\ResourceNotFoundException;
use App\Exports\OrderExport;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Maatwebsite\Excel\Facades\Excel;
use OpenApi\Annotations as OA;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class OrdersController
{
    public function __construct(public OrderService $orderService, public CommonService $commonService)
    {
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/order/list",
     *      operationId="AdminOrders",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Заказы"},
     *      summary="Список всех заказов",
     *     @OA\Parameter(
     *         name="filter[id]",
     *         in="query",
     *         description="ID заказа",
     *         @OA\Schema(
     *               type="integer",
     *             )
     *     ),
     *    @OA\Parameter(
     *          name="filter[user_application_id]",
     *          in="query",
     *          description="ID заявки",
     *          @OA\Schema(
     *                type="integer",
     *              )
     *      ),
     *    @OA\Parameter(
     *           name="filter[time_slot_start_id]",
     *           in="query",
     *           description="ID слота",
     *           @OA\Schema(
     *                 type="integer",
     *               )
     *       ),
     *     @OA\Parameter(
     *         name="filter[status]",
     *         in="query",
     *         description="Фильтр по статусу заказа",
     *         @OA\Schema(
     *               type="string",
     *               enum={"pending","processing", "completed","cancelled"},
     *         )
     *     ),
     *      @OA\Parameter(
     *           name="filter[stand_area]",
     *           in="query",
     *           description="Фильтр по лощади стенда",
     *           @OA\Schema(
     *                 type="string",
     *                 enum={"small","big"},
     *           )
     *       ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/vnd.api+json",
     *             @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                property="data",
     *                type="object",
     *                @OA\Property(property="id", type="integer", example="1"),
     *                @OA\Property(property="type", type="string", example="order"),
     *                @OA\Property(
     *                   property="attributes",
     *                   type="object",
     *                   @OA\Property(property="id", type="integer", example="1"),
     *                   @OA\Property(property="user_application_id", type="integer", example="1"),
     *                   @OA\Property(property="status", type="sting", example="pending", readOnly="true"),
     *                   @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                   @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                   @OA\Property(property="time_slot_start_id", type="integer", example="1"),
     *                   @OA\Property(property="additional_services", type="object"),
     *                   @OA\Property(property="hardware", type="object"),
     *                   ),
     *                   @OA\Property(
     *                       property="links",
     *                       type="object",
     *                       @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/lk/order/list?id=1")
     *                   ),
     *                   @OA\Property(
     *                      property="relationships",
     *                      type="object",
     *                      @OA\Property(
     *                         property="user_applications",
     *                         type="object"),
     *                         @OA\Property(
     *                            property="time_slot_start",
     *                           type="object"),
     *                        ),
     *                    ),
     *                   @OA\Property(property="metadata", type="object",
     *                   @OA\Property(property="message", type="string", example="Ok"),
     *                  ),
     *             ),
     *         )
     *     ),
     *   @OA\Response(response=401,description="Unauthenticated"),
     *   @OA\Response(response=400, description="Bad Request"),
     *   @OA\Response(response=404,description="not found"),
     *   @OA\Response(response=403,description="Forbidden",
     *           @OA\JsonContent(
     *               @OA\Property(property="errors", type="array",
     *               @OA\Items(
     *                   @OA\Property(property="status", example="403"),
     *                   @OA\Property(property="detail", example="User does not have the right roles.")
     *               ),
     *            ),
     *          ),
     *       ),
     * )
     **/
    public function list(OrderRequest $request)
    {
        $dataApp = $request->validated();
        try {
            return new OrderCollection($this->orderService->list($dataApp));
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при выводе список заказов', $e);
        }
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/admin/order/update/{id}",
     *      operationId="AdminUpdateOrder",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Заказы"},
     *      summary="Редактирование заказа",
     *      @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        @OA\Schema(
     *           type="integer",
     *           example="1",
     *        )
     *     ),
     *      @OA\Parameter(
     *           name="stand_area",
     *           in="query",
     *           description="Площадь стенда",
     *           @OA\Schema(
     *                 type="string",
     *                 enum={"small","big"},
     *               )
     *           ),
     *     @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *      type="object",
     *      required={"locate"},
     *          @OA\Property(property="time_slot_start_id", description="ID слота", type="integer",  example="1"),
     *          @OA\Property(property="time_slot_end_id", description="ID слота", type="integer",  example="2"),
     *          @OA\Property(property="user_applications_id", description="ID слота", type="integer",  example="1"),
     *          @OA\Property(property="status", description="ID слота", type="string",  example="processing"),
     *       ),
     *     ),
     *     ),
     *    @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *              property="data",
     *              type="object",
     *              @OA\Property(property="id", type="integer", example="1"),
     *              @OA\Property(property="type", type="string", example="order"),
     *              @OA\Property(
     *                 property="attributes",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example="1"),
     *                 @OA\Property(property="user_application_id", type="integer", example="1"),
     *                 @OA\Property(property="status", type="sting", example="pending", readOnly="true"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:15"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:15"),
     *                 @OA\Property(property="time_slot_start_id", type="integer", example="1"),
     *                 @OA\Property(property="time_slot_end_id", type="integer", example="1"),
     *                 @OA\Property(property="stand_area", type="string", example="small"),
     *                 ),
     *                 @OA\Property(
     *                    property="links",
     *                    type="object",
     *                    @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/lk/order/list?id=1")
     *                 ),
     *                 @OA\Property(
     *                    property="relationships",
     *                    type="object",
     *                    @OA\Property(
     *                       property="user_applications",
     *                       type="object"),
     *                       @OA\Property(
     *                           property="time_slot_start",
     *                           type="object"),
     *                       ),
     *                     ),
     *                   @OA\Property(property="metadata", type="object",
     *                   @OA\Property(property="message", type="string", example="Ok"),
     *                   ),
     *              ),
     *        )
     *   ),
     *    @OA\Response(response=401,description="Unauthenticated"),
     *    @OA\Response(response=400,description="Bad Request"),
     *    @OA\Response(response=404,description="not found"),
     *    @OA\Response(response=403,description="Forbidden",
     *        @OA\JsonContent(
     *            @OA\Property(property="errors", type="array",
     *            @OA\Items(
     *                @OA\Property(property="status", example="403"),
     *                @OA\Property(property="detail", example="User does not have the right roles.")
     *            ),
     *         ),
     *       ),
     *    ),
     * )
     * @param int $id
     * @param UpdateOrderRequest $request
     * @return ApiSuccessResponse|ResourceNotFoundException
     * @throws CustomException
     */
    public function update(int $id, UpdateOrderRequest $request): ApiSuccessResponse|ResourceNotFoundException
    {
        $dataApp = $request->validated();
        try {
            return new ApiSuccessResponse(
                new OrderResource($this->orderService->update($id, $dataApp)),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/order/{id}",
     *      operationId="AdminOrder",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Заказы"},
     *      summary="Получить данные заказа",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *            type="integer",
     *            example=1,
     *         )
     *      ),
     *      @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *            mediaType="application/vnd.api+json",
     *            @OA\Schema(
     *            type="object",
     *            @OA\Property(
     *               property="data",
     *               type="object",
     *               @OA\Property(property="id", type="integer", example="1"),
     *               @OA\Property(property="type", type="string", example="order"),
     *               @OA\Property(
     *                  property="attributes",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="user_application_id", type="integer", example="1"),
     *                  @OA\Property(property="status", type="sting", example="pending", readOnly="true"),
     *                  @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                  @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                  @OA\Property(property="time_slot_start_id", type="integer", example="1"),
     *                  @OA\Property(property="additional_services", type="object"),
     *                  @OA\Property(property="hardware", type="object"),
     *                  ),
     *                  @OA\Property(
     *                      property="links",
     *                      type="object",
     *                      @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/lk/order/list?id=1")
     *                  ),
     *                  @OA\Property(
     *                     property="relationships",
     *                     type="object",
     *                     @OA\Property(
     *                        property="user_applications",
     *                        type="object"),
     *                        @OA\Property(
     *                           property="time_slot_start",
     *                          type="object"),
     *                       ),
     *                   ),
     *                  @OA\Property(property="metadata", type="object",
     *                  @OA\Property(property="message", type="string", example="Ok"),
     *                 ),
     *            ),
     *        )
     *    ),
     *    @OA\Response(response=401,description="Unauthenticated"),
     *    @OA\Response(response=400,description="Bad Request"),
     *    @OA\Response(response=404,description="not found"),
     *    @OA\Response(response=403,description="Forbidden",
     *        @OA\JsonContent(
     *            @OA\Property(property="errors", type="array",
     *                @OA\Items(
     *                   @OA\Property(property="status", example="403"),
     *                   @OA\Property(property="detail", example="User does not have the right roles.")
     *                ),
     *            ),
     *        ),
     *    ),
     * )
     * @param int $id
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function show(int $id): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            new  OrderResource($this->orderService->show($id)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/order/export",
     *      operationId="exportOrders",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Заказы"},
     *      summary="Экспорт Заказов",
     *      @OA\Parameter(
     *         name="filter[from]",
     *         in="query",
     *         description="Начальная дата",
     *         @OA\Schema(
     *            type="sting",
     *            example="2023-11-27 15:15"
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="filter[to]",
     *          in="query",
     *          description="Конечная дата",
     *          @OA\Schema(
     *             type="string",
     *             example="2023-12-27 15:15"
     *          )
     *       ),
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
     *                  example="C:\xampp\htdocs\newapiartrussiafair\storage\framework\cache\orders\order-oY6rgr6iVUGEsmSZML87KgIN3KrJf35i.xlsx"
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
     *         @OA\JsonContent(
     *            @OA\Property(property="errors", type="array",
     *                @OA\Items(
     *                   @OA\Property(property="status", example="403"),
     *                   @OA\Property(property="detail", example="User does not have the right roles.")
     *                ),
     *            ),
     *         ),
     *     ),
     *     @OA\Response(response=500,description="Server error")
     * )
     * @param ExportOrdersRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function export(ExportOrdersRequest $request): ApiSuccessResponse
    {
        try {
            $fileName = '/orders/orders-' . date('Y-m-d_H-i-s') . '.xlsx';
            Excel::store(
                new OrderExport($this->commonService->dateInterval($request->validated())),
                $fileName
            );
            $link = storage_path('/app/' . $fileName);
            return new ApiSuccessResponse($link, ['message' => 'Ok'], ResponseAlias::HTTP_OK);
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception|Exception $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

}
