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
use Lk\Http\Requests\AdditionalService\ListAdditionalServiceRequest;
use Lk\Http\Requests\AdditionalService\ShowAdditionalServiceRequest;
use Lk\Http\Requests\AdditionalService\StoreAdditionalServiceRequest;
use Lk\Http\Requests\AdditionalService\UpdateAdditionalServiceRequest;
use Lk\Http\Resources\AdditionalService\AdditionalServiceResource;
use Lk\Services\AdditionalServicesService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AdditionalServicesController extends Controller
{

    private  OrderItemTypesEnum $type;

    public function __construct(public AdditionalServicesService $service)
    {
        $this->type = OrderItemTypesEnum::ADDITIONAL_SERVICE();
    }

    /**
     * @OA\Post(
     *      path="/api/v1/lk/additional-service",
     *      operationId="LkServiceStore",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Дополнительные Услуги"},
     *      summary="Создание новой доп. услуги",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            required={"order_id","quantity", "price", "name", "order_id"},
     *            @OA\Property(property="quantity", description="Количество", type="integer", example="1"),
     *            @OA\Property(property="service_catalog_id", description="ID сервиса", type="integer", example="1"),
     *            @OA\Property(property="order_id", description="ID заказа", type="integer", example="1"),
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
     *             @OA\Property(property="id", example="1", type="integer"),
     *             @OA\Property(property="type", example="order-item", type="string"),
     *                @OA\Property(property="attributes", type="object",
     *                @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                @OA\Property(property="quantity", description="количество", type="integer", example="1" ),
     *                @OA\Property(property="order_id", description="ID элемента заказа", type="integer", example="1"),
     *                @OA\Property(property="service_catalog_id", description="ID сервиса", type="integer", example="1"),
     *                @OA\Property(property="created_at", description="Дата и время создание элемента заказа", type="string", example="2023-11-29T08:43:13.000000Z"),
     *                @OA\Property(property="updated_at", description="Дата и время обновление элемента заказа", type="string", example="2023-11-29T08:43:13.000000Z"),
     *                @OA\Property(property="orders", description="Заказ", type="object"),
     *             ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/order/order-item/show/2"),
     *              ),
     *             @OA\Property(property="relationships", type="object",
     *                @OA\Property(property="orders", type="object",
     *                  @OA\Property(property="data",type="object"),
     *                  @OA\Property(property="links",type="object",
     *                     @OA\Property(property="self",type="string",example="http://newapiartrussiafair/api/v1/lk/order-item/2/relationships/orders"),
     *                     @OA\Property(property="related", type="string", example="http://newapiartrussiafair/api/v1/lk/order-item/2/orders" ),
     *                 ),
     *                ),
     *              @OA\Property(property="service_catalogs", type="object",
     *                 @OA\Property(property="data",type="object"),
     *                 @OA\Property(property="links",type="object",
     *                     @OA\Property(property="self",type="string",example="http://newapiartrussiafair/api/v1/lk/additional-service/20/relationships/service_catalogs"),
     *                     @OA\Property(property="related", type="string", example="http://newapiartrussiafair/api/v1/lk/additional-service/20/service_catalogs" ),
     *                 ),
     *                ),
     *              ),
     *            ),
     *                @OA\Property(property="metadata",type="object",
     *                   @OA\Property(property="message", example="Допольнительная услуга успешно создан"),
     *                 ),
     *              ),
     *             ),
     *           ),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=400,description="Bad Request"),
     *      @OA\Response(response=404,description="not found"),
     *            @OA\Response(response=403,description="Forbidden",
     *           @OA\JsonContent(
     *               @OA\Property(property="errors", type="array",
     *               @OA\Items(
     *                   @OA\Property(property="status", example="403"),
     *                   @OA\Property(property="detail", example="User does not have the right roles.")
     *               ),
     *            ),
     *          ),
     *       ),
     *      @OA\Response(response=500,description="Server error")
     * )
     * @param StoreAdditionalServiceRequest $request
     * @return ApiErrorResponse|ApiSuccessResponse
     */
    public function store(StoreAdditionalServiceRequest $request): ApiErrorResponse|ApiSuccessResponse
    {
        try {
            $user = Auth::user();
            $dataApp = $request->validated();
            $dataApp['type'] = $this->type;
            $dataApp['user_id'] = $user->id;
            $orderItem = $this->service->create($dataApp);
            return new ApiSuccessResponse(
                new  AdditionalServiceResource($orderItem),
                ['message' => 'Допольнительная услуга успешно создан'],
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при создании доп. услуги', $e);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/lk/additional-service/list",
     *      operationId="LkServiceList",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Дополнительные Услуги"},
     *      summary="Получить список доп. услуг",
     *     @OA\Parameter(
     *         name="filter[id]",
     *         in="query",
     *         description="ID Доп услуги",
     *         @OA\Schema(
     *               type="integer",
     *             )
     *     ),
     *    @OA\Parameter(
     *          name="filter[order_id]",
     *          in="query",
     *          description="ID Заказа",
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
     *             @OA\Property(
     *             property="data",
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="quantity", description="количество", type="integer", example="1" ),
     *                 @OA\Property(property="order_id", description="ID элемента заказа", type="integer", example="1"),
     *                 @OA\Property(property="service_catalog_id", description="ID сервиса", type="integer", example="1"),
     *                 @OA\Property(property="created_at", description="Дата и время создание элемента заказа", type="string", example="2023-11-29T08:43:13.000000Z"),
     *                 @OA\Property(property="updated_at", description="Дата и время обновление элемента заказа", type="string", example="2023-11-29T08:43:13.000000Z"),
     *            ),
     *          ),
     *                @OA\Property(property="metadata",type="object",
     *                   @OA\Property(property="message", example="Ok"),
     *                 ),
     *               ),
     *             ),
     *           ),
     *     @OA\Response(response=401,description="Unauthenticated"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=404,description="not found"),
     *          @OA\Response(response=403,description="Forbidden",
     *           @OA\JsonContent(
     *               @OA\Property(property="errors", type="array",
     *               @OA\Items(
     *                   @OA\Property(property="status", example="403"),
     *                   @OA\Property(property="detail", example="User does not have the right roles.")
     *               ),
     *            ),
     *          ),
     *       ),
     *     @OA\Response(response=500,description="Server error, not found"),
     * )
     * @param ListAdditionalServiceRequest $request
     * @return ApiErrorResponse|ApiSuccessResponse
     */
    public function list(ListAdditionalServiceRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $user = Auth::user();
            $dataApp = $request->validated();
            return new ApiSuccessResponse(
                $this->service->list($user->id, $dataApp),
                ['message' => 'Ok'],
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при получение элеметов заказа', $e);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/lk/additional-service/{id}",
     *      operationId="LkServiceShow",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Дополнительные Услуги"},
     *      summary="Получить данные доп услуг",
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
     *             @OA\Property(property="id", example="1", type="integer"),
     *             @OA\Property(property="type", example="additional-service", type="string",),
     *                @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="order_id", description="ID элемента заказа", type="integer", example="1"),
     *                 @OA\Property(property="service_catalog_id", description="ID сервиса", type="integer", example="1"),
     *                 @OA\Property(property="created_at", description="Дата и время создание элемента заказа", type="string", example="2023-11-29T08:43:13.000000Z"),
     *                 @OA\Property(property="updated_at", description="Дата и время обновление элемента заказа", type="string", example="2023-11-29T08:43:13.000000Z"),
     *                 @OA\Property(property="service_catalogs", description="Заказ", type="object"),
     *                 @OA\Property(property="orders", description="Заказ", type="object"),
     *             ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/order/order-item/show/2"),
     *              ),
     *             @OA\Property(property="relationships", type="object",
     *              @OA\Property(property="orders", type="object",
     *                 @OA\Property(property="data", type="object"),
     *                 @OA\Property(property="links",type="object",
     *                     @OA\Property(property="self",type="string",example="http://newapiartrussiafair/api/v1/lk/order-item/2/relationships/orders"),
     *                     @OA\Property(property="related", type="string", example="http://newapiartrussiafair/api/v1/lk/order-item/2/orders" ),
     *                 ),
     *                ),
     *              @OA\Property(property="service_catalogs", type="object",
     *                  @OA\Property(property="data", type="object"),
     *                  @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string",example="http://newapiartrussiafair/api/v1/lk/additional-service/1/relationships/service_catalogs"),
     *                      @OA\Property(property="related", type="string", example="http://newapiartrussiafair/api/v1/lk/additional-service/1/service_catalogs" ),
     *                  ),
     *                 ),
     *              ),
     *            ),
     *                @OA\Property(property="metadata",type="object",
     *                   @OA\Property(property="message", example="Ok"),
     *                 ),
     *               ),
     *             ),
     *           ),
     *    @OA\Response(response=401,description="Unauthenticated"),
     *    @OA\Response(response=400, description="Bad Request"),
     *    @OA\Response(response=404,description="not found"),
     *    @OA\Response(response=403,description="Forbidden",
     *           @OA\JsonContent(
     *               @OA\Property(property="errors", type="array",
     *               @OA\Items(
     *                   @OA\Property(property="status", example="403"),
     *                   @OA\Property(property="detail", example="User does not have the right roles.")
     *               ),
     *            ),
     *          ),
     *       ),
     *    @OA\Response(response=500,description="Server error, not found"),
     * )
     * @param int $id
     * @param ShowAdditionalServiceRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function show(int $id, ShowAdditionalServiceRequest $request): ApiSuccessResponse
    {
        $user = Auth::user();
        $dataApp = $request->validated();
        return new ApiSuccessResponse(
            new AdditionalServiceResource($this->service->show($id, $user->id, $dataApp)),
            ['message' => 'Ok'],
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Patch(
     *    path="/api/v1/lk/additional-service/update/{id}",
     *    operationId="lkServiceUpdate",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Дополнительные Услуги"},
     *    summary="Редактирование доп. услугу",
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
     *             @OA\Property(property="quantity", description="Количество", type="integer", example="1"),
     *             @OA\Property(property="order_id", description="ID заказа", type="integer", example="1"),
     *             @OA\Property(property="service_catalog_id", description="ID сервиса", type="integer", example="1"),
     *         ),
     *       ),
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            @OA\Property(
     *              property="data",
     *              description="ID категории",
     *              type="object",
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="order-item", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="quantity", description="количество", type="integer", example="1" ),
     *                 @OA\Property(property="service_catalog_id", description="ID сервиса", type="integer", example="1"),
     *                 @OA\Property(property="order_id", description="ID элемента заказа", type="integer", example="1"),
     *                 @OA\Property(property="created_at", description="Дата и время создание элемента заказа", type="string", example="2023-11-29T08:43:13.000000Z"),
     *                 @OA\Property(property="updated_at", description="Дата и время обновление элемента заказа", type="string", example="2023-11-29T08:43:13.000000Z"),
     *                 @OA\Property(property="orders", description="Заказ", type="object"),
     *                 @OA\Property(property="service_catalogs", description="Сервис", type="object"),
     *              ),
     *              @OA\Property(property="links", type="object",
     *                  @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/order/order-item/show/2"),
     *               ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="orders", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string",example="http://newapiartrussiafair/api/v1/lk/order-item/2/relationships/orders"),
     *                      @OA\Property(property="related", type="string", example="http://newapiartrussiafair/api/v1/lk/order-item/2/orders" ),
     *                  ),
     *                 ),
     *                 @OA\Property(property="service_catalogs", type="object",
     *                     @OA\Property(property="data",type="object"),
     *                     @OA\Property(property="links",type="object",
     *                       @OA\Property(property="self",type="string",example="http://newapiartrussiafair/api/v1/lk/additional-service/20/relationships/service_catalogs"),
     *                       @OA\Property(property="related", type="string", example="http://newapiartrussiafair/api/v1/lk/additional-service/20/service_catalogs" ),
     *                       ),
     *                     ),
     *                   ),
     *                ),
     *             ),
     *                 @OA\Property(property="metadata",type="object",
     *                    @OA\Property(property="message", example="Допольнительная услуга обновлена"),
     *                  ),
     *                ),
     *              ),
     *            ),
     *      @OA\Response(response=401,description="Unauthenticated"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404,description="not found"),
     *            @OA\Response(response=403,description="Forbidden",
     *           @OA\JsonContent(
     *               @OA\Property(property="errors", type="array",
     *               @OA\Items(
     *                   @OA\Property(property="status", example="403"),
     *                   @OA\Property(property="detail", example="User does not have the right roles.")
     *               ),
     *            ),
     *          ),
     *       ),
     *      @OA\Response(response=500,description="Server error, not found")
     * )
     * @param int $id
     * @param UpdateAdditionalServiceRequest $request
     * @return ApiErrorResponse|ApiSuccessResponse
     * @throws CustomException
     */
    public function update(int $id, UpdateAdditionalServiceRequest $request): ApiSuccessResponse|ResourceNotFoundException
    {
        $user = Auth::user();
        $dataApp = $request->validated();
        try {
            return new ApiSuccessResponse(
                new AdditionalServiceResource($this->service->update($id, $user->id, $dataApp)),
                ['message' => 'Допольнительная услуга обновлена'],
                Response::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }
}
