<?php

namespace Lk\Http\Controllers;

use App\Exceptions\CustomException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Auth;
use Lk\Http\Requests\Order\ShowOrderRequest;
use Lk\Http\Requests\Order\UpdateOrderRequest;
use Lk\Http\Resources\Order\OrderCollection;
use Lk\Http\Resources\Order\OrderResource;
use Lk\Services\OrderService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class OrdersController extends Controller
{

    public function __construct(public OrderService $orderService)
    {}

    /**
     * @OA\Get(
     *      path="/api/v1/lk/order/list",
     *      operationId="LkPartisaipantOrders",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Заказы"},
     *      summary="Получить список заказов участника",
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
     *     @OA\Parameter(
     *         name="filter[status]",
     *         in="query",
     *         description="Фильтр по статусу заказа",
     *         @OA\Schema(
     *               type="string",
     *               enum={"pending","processing", "completed","cancelled"},
     *         )
     *     ),
     *     @OA\Parameter(
     *          name="filter[stand_area]",
     *          in="query",
     *          description="Фильтр по лощади стенда",
     *          @OA\Schema(
     *                type="string",
     *                enum={"small","big"},
     *          )
     *      ),
     *     @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
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
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="time_slot_start_id", type="integer", example="1"),
     *                 @OA\Property(property="time_slot_end_id", type="integer", example="2"),
     *                 @OA\Property(property="stand_area", type="string", example="small"),
     *                 @OA\Property(property="hardware", type="object"),
     *                 @OA\Property(property="additional_service", type="object"),
     *              ),
     *              @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/lk/order/show/1")
     *              ),
     *              @OA\Property(property="relationships",type="object",
     *                 @OA\Property(property="user_applications",type="object"),
     *                 @OA\Property(property="time_slot_start",type="object"),
     *                   @OA\Property(property="links",type="object"),
     *                   ),
     *                ),
     *              @OA\Property(property="links", type="object",
     *              @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/lk/order/list"),
     *             ),
     *          ),
     *       )
     *    ),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=400,description="Bad Request"),
     *   @OA\Response(response=404,description="not found"),
     *   @OA\Response(response=403,description="Forbidden",
     *       @OA\JsonContent(
     *          @OA\Property(property="errors", type="array",
     *             @OA\Items(
     *                @OA\Property(property="status", example="403"),
     *                 @OA\Property(property="detail", example="User does not have the right roles.")
     *              ),
     *          ),
     *        ),
     *    ),
     * )
     * @param ShowOrderRequest $request
     * @return ApiErrorResponse|OrderCollection
     */
    public function list(ShowOrderRequest $request):ApiErrorResponse|OrderCollection
    {
        $user = Auth::user();
        $dataApp = $request->validated();
        try {
            return  new OrderCollection($this->orderService->list($user->id, $dataApp));
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при выводе список заказов', $e);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/lk/order/{id}",
     *      operationId="LkPartisaipantOrder",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Заказы"},
     *      summary="Получить заказов участника по ID",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *            type="integer",
     *            example="1",
     *         )
     *      ),
     *     @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
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
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="time_slot_start_id", type="integer", example="1"),
     *                 @OA\Property(property="time_slot_end_id", type="integer", example="2"),
     *                 @OA\Property(property="stand_area", type="string", example="small"),
     *              ),
     *              @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/lk/order/show/1")
     *              ),
     *              @OA\Property(property="relationships",type="object",
     *                 @OA\Property(property="user_applications",type="object"),
     *                 @OA\Property(property="time_slot_start",type="object"),
     *                 @OA\Property(property="links",type="object"),
     *                 ),
     *                ),
     *              @OA\Property(property="links", type="object",
     *              @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/lk/order/list"),
     *             ),
     *          ),
     *       )
     *    ),
     *   @OA\Response(response=401,description="Unauthenticated"),
     *   @OA\Response(response=400,description="Bad Request"),
     *   @OA\Response(response=404,description="Not found"),
     *   @OA\Response(response=403,description="Forbidden",
     *       @OA\JsonContent(
     *          @OA\Property(property="errors", type="array",
     *             @OA\Items(
     *                 @OA\Property(property="status", example="403"),
     *                 @OA\Property(property="detail", example="User does not have the right roles.")
     *             ),
     *          ),
     *      ),
     *   ),
     * )
     * @param int $id
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function show(int $id): ApiSuccessResponse
    {
        $user = Auth::user();
        return new ApiSuccessResponse(
            new  OrderResource($this->orderService->show($id, $user->id)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );

    }

    /**
     * @OA\Patch(
     *      path="/api/v1/lk/order/update/{id}",
     *      operationId="LkUpdateOrder",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Заказы"},
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
     *          name="stand_area",
     *          in="query",
     *          description="Площадь стенда",
     *          @OA\Schema(
     *                type="string",
     *                enum={"small","big"},
     *              )
     *          ),
     *     @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *        type="object",
     *        required={"locate"},
     *        @OA\Property(property="time_slot_start_id", description="ID слота", type="integer",  example="1"),
     *        @OA\Property(property="time_slot_end_id", description="ID слота", type="integer",  example="2")
     *       ),
     *       ),
     *     ),
     *     @OA\Response(
     *       response=200,
     *       description="Success",
     *       @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *          type="object",
     *          @OA\Property(
     *             property="data",
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="type", type="string", example="order"),
     *             @OA\Property(
     *                property="attributes",
     *                type="object",
     *                @OA\Property(property="id", type="integer", example="1"),
     *                @OA\Property(property="user_application_id", type="integer", example="1"),
     *                @OA\Property(property="status", type="sting", example="pending", readOnly="true"),
     *                @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                @OA\Property(property="time_slot_start_id", type="integer", example="1"),
     *                @OA\Property(property="time_slot_end_id", type="integer", example="2"),
     *                @OA\Property(property="stand_area", type="string", example="small"),
     *                @OA\Property(property="deleted_at", type="string", example="null"),
     *             ),
     *             @OA\Property(
     *                property="links",
     *                type="object",
     *                @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/lk/order/list?id=1")
     *             ),
     *             @OA\Property(
     *                property="relationships",
     *                type="object",
     *                @OA\Property(
     *                property="user_applications",
     *                type="object"),
     *                @OA\Property(
     *                  property="time_slot_start",
     *                  type="object"),
     *                  @OA\Property(
     *                  property="order_items",
     *                  type="object"),
     *                  @OA\Property(
     *                     property="links",
     *                      type="object"),
     *                  ),
     *               ),
     *             @OA\Property(property="metadata", type="object",
     *             @OA\Property(property="message", type="string", example="Ok"),
     *            ),
     *         ),
     *      )
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=400, description="Bad Request"),
     *   @OA\Response(response=404, description="not found"),
     *   @OA\Response(response=403,description="Forbidden",
     *       @OA\JsonContent(
     *          @OA\Property(property="errors", type="array",
     *             @OA\Items(
     *                @OA\Property(property="status", example="403"),
     *                @OA\Property(property="detail", example="User does not have the right roles.")
     *             ),
     *          ),
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
        $user = Auth::user();
        $dataApp = $request->validated();
        try {
            return new ApiSuccessResponse(
                new OrderResource($this->orderService->update($id, $user->id, $dataApp)),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (CustomException $e) {
            throw  new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }
}
