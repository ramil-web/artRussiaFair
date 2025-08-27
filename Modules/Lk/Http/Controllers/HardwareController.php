<?php

namespace Lk\Http\Controllers;

use App\Enums\OrderItemTypesEnum;
use App\Exceptions\CustomException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Lk\Http\Requests\Hardware\ShowHardwareRequest;
use Lk\Http\Requests\Hardware\StoreHardwareRequest;
use Lk\Http\Requests\Hardware\UpdateHardwareRequest;
use Lk\Http\Resources\Hardware\HardwareResource;
use Lk\Services\HardwareService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class HardwareController extends Controller
{

    private  OrderItemTypesEnum $type;

    public function __construct(public HardwareService $service)
    {
        $this->type = OrderItemTypesEnum::HARDWARE();
    }

    /**
     * @OA\Post(
     *      path="/api/v1/lk/hardware",
     *      operationId="LkHardwareStore",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Оборудование в аренду"},
     *      summary="Создание новой Оборудование в аренду",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            required={"order_id","quantity"},
     *            @OA\Property(property="quantity", description="Количество", type="integer", example="1"),
     *            @OA\Property(property="order_id", description="ID заказа", type="integer", example="1"),
     *            @OA\Property(property="product_id", description="ID продукта", type="integer", example="1"),
     *             ),
     *          ),
     *      ),
     *      @OA\Response(
     *       response=200,
     *       description="Success",
     *       @OA\MediaType(
     *         mediaType="application/vnd.api+json",
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="data",
     *             description="ID категории",
     *             type="object",
     *             @OA\Property(property="id", example="1", type="integer", ),
     *             @OA\Property(property="type", example="hardware", type="string",),
     *                @OA\Property(property="attributes", type="object",
     *                @OA\Property(property="id", description="Порядковый номер оборудование", type="integer", example="1"),
     *                @OA\Property(property="quantity", description="количество", type="integer", example="1" ),
     *                @OA\Property(property="order_id", description="ID  заказа", type="integer", example="1"),
     *                @OA\Property(property="product_id", description="ID продукта", type="integer", example="1"),
     *                @OA\Property(property="created_at", description="Дата и время создание Оборудование в аренду", type="string", example="2023-11-29T08:43:13.000000Z"),
     *                @OA\Property(property="updated_at", description="Дата и время обновление Оборудование в аренду", type="string", example="2023-11-29T08:43:13.000000Z"),
     *                @OA\Property(property="products",  type="object"),
     *             ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/hardware/show/2"),
     *              ),
     *             @OA\Property(property="relationships", type="object",
     *                @OA\Property(property="orders", type="object",
     *                 @OA\Property(property="data", type="object"),
     *                 @OA\Property(property="links",type="object",
     *                     @OA\Property(property="self",type="string",example="http://newapiartrussiafair/api/v1/lk/hardware/orders"),
     *                     @OA\Property(property="related", type="string", example="http://newapiartrussiafair/api/v1/lk/hardware/2/orders" ),
     *                 ),
     *                ),
     *             @OA\Property(
     *                 property="products", type="object",
     *                 @OA\Property(property="data", type="object"),
     *                 @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self", example="http://newapiartrussiafair/api/v1/lk/hardware/36/relationships/products"),
     *                   @OA\Property(property="related", example="http://newapiartrussiafair/api/v1/lk/hardware/36/products")
     *                ),
     *               ),
     *              ),
     *            ),
     *                @OA\Property(property="metadata",type="object",
     *                   @OA\Property(property="message", example="Оборудование на аренду успешно создан"),
     *                 ),
     *              ),
     *             ),
     *           ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=400,description="Bad Request"),
     *     @OA\Response(response=404,description="Not found"),
     *     @OA\Response(response=403,description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="array",
     *             @OA\Items(
     *                @OA\Property(property="status", example="403"),
     *                @OA\Property(property="detail", example="User does not have the right roles.")
     *             ),
     *         ),
     *       ),
     *    ),
     *    @OA\Response(response=500,description="Server error")
     * )
     * @param StoreHardwareRequest $request
     * @return ApiErrorResponse|ApiSuccessResponse
     */
    public function store(StoreHardwareRequest $request): ApiErrorResponse|ApiSuccessResponse
    {
        try {
            $user = Auth::user();
            $dataApp = $request->validated();
            $dataApp['type'] = $this->type;
            $dataApp['user_id'] = $user->id;
            $orderItem = $this->service->create($dataApp);
            return new ApiSuccessResponse(
                new  HardwareResource($orderItem),
                ['message' => 'Оборудование на аренду успешно создан'],
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при создании Оборудование на аренду', $e);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/lk/hardware/list",
     *      operationId="LkHardwareList",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Оборудование в аренду"},
     *      summary="Получить список Оборудование на аренду",
     *     @OA\Parameter(
     *         name="filter[id]",
     *         in="query",
     *         description="ID Оборудование в аренду",
     *         @OA\Schema(
     *               type="integer",
     *             )
     *     ),
     *    @OA\Parameter(
     *          name="filter[order_id]",
     *          in="query",
     *          description="ID заказа",
     *          @OA\Schema(
     *                type="integer",
     *              )
     *      ),
     *     @OA\Response(
     *       response=200,
     *       description="Success",
     *       @OA\MediaType(
     *         mediaType="application/vnd.api+json",
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="data",
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", description="Порядковый номер элемента", type="integer", example="1"),
     *                 @OA\Property(property="quantity", description="количество", type="integer", example="1" ),
     *                 @OA\Property(property="order_id", description="ID Оборудование в аренду", type="integer", example="1"),
     *                 @OA\Property(property="product_id", description="ID продукта", type="integer", example="1"),
     *                 @OA\Property(property="created_at", description="Дата и время создания Оборудование в аренду", type="string", example="2023-11-29T08:43:13.000000Z"),
     *                 @OA\Property(property="updated_at", description="Заказ", type="string", example="2023-11-29T08:43:13.000000Z"),
     *             ),
     *            ),
     *          ),
     *                @OA\Property(property="metadata",type="object",
     *                   @OA\Property(property="message", example="Ok"),
     *                 ),
     *               ),
     *             ),
     *     @OA\Response(response=401,description="Unauthenticated"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=404,description="Not found"),
     *     @OA\Response(response=403,description="Forbidden",
     *         @OA\JsonContent(
     *            @OA\Property(property="errors", type="array",
     *            @OA\Items(
     *               @OA\Property(property="status", example="403"),
     *               @OA\Property(property="detail", example="User does not have the right roles.")
     *           ),
     *         ),
     *       ),
     *     ),
     *     @OA\Response(response=500,description="Server error, not found"),
     * )
     * @param ShowHardwareRequest $request
     * @return ApiErrorResponse|ApiSuccessResponse
     */
    public function list(ShowHardwareRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $user = Auth::user();
            $dataApp = $request->validated();
            return new ApiSuccessResponse($this->service->list($user->id, $dataApp),['message' => 'Ok'],Response::HTTP_OK);
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при получение Оборудование на аренду', $e);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/lk/hardware/{id}",
     *      operationId="LkHardwareShow",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Оборудование в аренду"},
     *      summary="Получить данные Оборудование на аренду",
     *      @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        @OA\Schema(
     *              type="integer",
     *              example="1",
     *          )
     *      ),
     *     @OA\Response(
     *       response=200,
     *       description="Success",
     *       @OA\MediaType(
     *         mediaType="application/vnd.api+json",
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="data",
     *             description="ID категории",
     *             type="object",
     *             @OA\Property(property="id", example="1", type="integer", ),
     *             @OA\Property(property="type", example="hardware", type="string",),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Порядковый номер оборудование", type="integer", example="1"),
     *                 @OA\Property(property="quantity", description="количество", type="integer", example="1" ),
     *                 @OA\Property(property="order_id", description="ID  заказа", type="integer", example="1"),
     *                 @OA\Property(property="product_id", description="ID  продукта", type="integer", example="1"),
     *                 @OA\Property(property="created_at", description="Дата и время создание Оборудование в аренду", type="string", example="2023-11-29T08:43:13.000000Z"),
     *                 @OA\Property(property="updated_at", description="Дата и время обновление Оборудование в аренду", type="string", example="2023-11-29T08:43:13.000000Z"),
     *                 @OA\Property(property="orders", description="Заказ", type="object"),
     *                 @OA\Property(property="products", description="Продукт", type="object"),
     *              ),
     *              @OA\Property(property="links", type="object",
     *                  @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/hardware/show/2"),
     *               ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="orders", type="object",
     *                  @OA\Property(property="data", type="object",),
     *                  @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string",example="http://newapiartrussiafair/api/v1/lk/hardware/orders"),
     *                      @OA\Property(property="related", type="string", example="http://newapiartrussiafair/api/v1/lk/hardware/2/orders" ),
     *                  ),
     *                 ),
     *                @OA\Property(property="products", type="object",
     *                   @OA\Property(property="data", type="object",),
     *                   @OA\Property(property="links",type="object",
     *                       @OA\Property(property="self",type="string",example="http://newapiartrussiafair/api/v1/lk/hardware/26/relationships/products"),
     *                       @OA\Property(property="related", type="string", example="http://newapiartrussiafair/api/v1/lk/hardware/26/products"),
     *                   ),
     *                 ),
     *               ),
     *             ),
     *                @OA\Property(property="metadata",type="object",
     *                   @OA\Property(property="message", example="Ok"),
     *                 ),
     *              ),
     *             ),
     *           ),
     *    @OA\Response(response=401,description="Unauthenticated"),
     *    @OA\Response(response=400, description="Bad Request"),
     *    @OA\Response(response=404,description="Not found"),
     *    @OA\Response(response=403,description="Forbidden",
     *        @OA\JsonContent(
     *           @OA\Property(property="errors", type="array",
     *           @OA\Items(
     *              @OA\Property(property="status", example="403"),
     *              @OA\Property(property="detail", example="User does not have the right roles.")
     *           ),
     *        ),
     *     ),
     *   ),
     *    @OA\Response(response=500,description="Server error, not found")
     *   )
     * @param int $id
     * @param ShowHardwareRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function show(int $id, ShowHardwareRequest $request): ApiSuccessResponse
    {
        $user = Auth::user();
        $dataApp = $request->validated();
        return new ApiSuccessResponse(
            new HardwareResource($this->service->show($id, $user->id, $dataApp)),
            ['message' => 'Ok'],
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Patch(
     *    path="/api/v1/lk/hardware/update/{id}",
     *    operationId="lkHardwareUpdate",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Оборудование в аренду"},
     *    summary="Редактирование Оборудование на аранду",
     *    @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       @OA\Schema(
     *             type="integer",
     *             example="1",
     *         )
     *     ),
     *    @OA\RequestBody(
     *    required=true,
     *    @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *           type="object",
     *              @OA\Property(property="quantity", description="Количество", type="integer", example="1"),
     *              @OA\Property(property="order_id", description="ID заказа", type="integer", example="1"),
     *              @OA\Property(property="product_id", description="ID продукта", type="integer", example="1"),
     *         ),
     *       ),
     *    ),
     *    @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *          type="object",
     *          @OA\Property(
     *            property="data",
     *            description="ID категории",
     *            type="object",
     *            @OA\Property(property="id", example="1", type="integer", ),
     *              @OA\Property(property="type", example="hardware", type="string",),
     *                  @OA\Property(property="attributes", type="object",
     *                  @OA\Property(property="id", description="Порядковый номер оборудование", type="integer", example="1"),
     *                  @OA\Property(property="quantity", description="количество", type="integer", example="1" ),
     *                  @OA\Property(property="price", description="Стоимость", type="float", example="18000" ),
     *                  @OA\Property(property="article", description="Арткул", type="string", example="12121-lb"),
     *                  @OA\Property(property="type", description="Тип товара/услуги", type="string", example="rental" ),
     *                  @OA\Property(property="name", type="object",
     *                      @OA\Property(property="ru", type="object", description="Описание выставки", type="string", example="Тестовое название услуги"),
     *                  ),
     *                  @OA\Property(property="specifications", description="Характеристики", type="object",
     *                     @OA\Property(property="ru", type="object",
     *                        @OA\Property(property="width", description="Ширина", type="integer", example="10"),
     *                        @OA\Property(property="height", description="Высота", type="integer", example="20"),
     *                        @OA\Property(property="depth", description="Глубина", type="integer", example="5")
     *                     ),
     *                  ),
     *                  @OA\Property(property="order_id", description="ID  заказа", type="integer", example="1"),
     *                  @OA\Property(property="created_at", description="Дата и время создание Оборудование в аренду", type="string", example="2023-11-29T08:43:13.000000Z"),
     *                  @OA\Property(property="updated_at", description="Дата и время обновление Оборудование в аренду", type="string", example="2023-11-29T08:43:13.000000Z"),
     *                  @OA\Property(property="orders", description="Заказ", type="object"),
     *               ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/hardware/show/2"),
     *                ),
     *               @OA\Property(property="relationships", type="object",
     *                  @OA\Property(property="orders", type="object",
     *                   @OA\Property(property="data", type="object",),
     *                   @OA\Property(property="links",type="object",
     *                       @OA\Property(property="self",type="string",example="http://newapiartrussiafair/api/v1/lk/hardware/orders"),
     *                       @OA\Property(property="related", type="string", example="http://newapiartrussiafair/api/v1/lk/hardware/2/orders" ),
     *                   ),
     *                  ),
     *                ),
     *              ),
     *               @OA\Property(property="metadata",type="object",
     *                  @OA\Property(property="message", example="Ok"),
     *                ),
     *             ),
     *            ),
     *          ),
     *     @OA\Response(response=401,description="Unauthenticated"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=404,description="Not found"),
     *     @OA\Response(response=403,description="Forbidden",
     *         @OA\JsonContent(
     *            @OA\Property(property="errors", type="array",
     *            @OA\Items(
     *                @OA\Property(property="status", example="403"),
     *                @OA\Property(property="detail", example="User does not have the right roles.")
     *            ),
     *         ),
     *       ),
     *     ),
     *    @OA\Response(response=500,description="Server error, not found")
     * )
     * @param int $id
     * @param UpdateHardwareRequest $request
     * @return ApiErrorResponse|ApiSuccessResponse
     * @throws CustomException
     */
    public function update(int $id, UpdateHardwareRequest $request): ApiSuccessResponse|ResourceNotFoundException
    {
        $user = Auth::user();
        $dataApp = $request->validated();
        try {
            return new ApiSuccessResponse(
                new HardwareResource($this->service->update($id, $user->id, $dataApp)),
                ['message' => 'Ok'],
                Response::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }
}
