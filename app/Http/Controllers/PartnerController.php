<?php

namespace App\Http\Controllers;

use App\Http\Requests\Partner\PartnerRequest;
use App\Http\Resources\Partner\PartnerCollection;
use App\Http\Responses\ApiErrorResponse;
use App\Services\PartnerService;
use OpenApi\Annotations as OA;
use Throwable;

class PartnerController extends Controller
{

    public function __construct(public PartnerService $partnerService)
    {
    }

    /**
     * @OA\Get(
     *    path="/api/v1/partners",
     *    operationId="AppApartPartners",
     *    tags={"App|Партнёры"},
     *    summary="Получить список партнёрыов",
     *    @OA\Parameter(
     *         name="filter[category]",
     *         in="query",
     *         description="Фильтр по category событие, пока modern|classic",
     *         required=false,
     *         @OA\Schema(
     *            type="string",
     *            )
     *         ),
     *    @OA\Parameter(
     *       name="filter[id]",
     *       in="query",
     *       description="ID партнёра",
     *       @OA\Schema(
     *          type="integer",
     *        )
     *     ),
     *    @OA\Parameter(
     *       name="filter[event_id]",
     *       in="query",
     *       description="ID события",
     *       @OA\Schema(
     *           type="integer",
     *        )
     *    ),
     *    @OA\Parameter(
     *       name="filter[partner_category_id]",
     *       in="query",
     *       description="Фильтр по категории партнёра",
     *       @OA\Schema(
     *          type="integer",
     *       )
     *     ),
     *     @OA\Parameter(
     *         name="filter[name]",
     *         in="query",
     *         description="Имя партнёра",
     *         @OA\Schema(
     *            type="string",
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="filter[important]",
     *          in="query",
     *          description="Флаг для попадания на главную",
     *          @OA\Schema(
     *             type="boolean",
     *          )
     *       ),
     *      @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Сортировка по поле",
     *         @OA\Schema(
     *            type="string",
     *            enum={"id","sort_id","name","partner_category_id","stand_id","created_at", "updated_at"},
     *          )
     *      ),
     *      @OA\Parameter(
     *         name="order_by",
     *         in="query",
     *         description="Порядок сортировки",
     *         @OA\Schema(
     *            type="string",
     *            enum={"ASC", "DESC"},
     *          )
     *       ),
     *       @OA\Parameter(
     *             name="page",
     *             in="query",
     *             description="Номер страницы",
     *             @OA\Schema(
     *                type="integer",
     *                example=1
     *            )
     *        ),
     *       @OA\Parameter(
     *           name="per_page",
     *           in="query",
     *           description="Количество элементов на странице",
     *           @OA\Schema(
     *              type="integer",
     *               example=10
     *            )
     *       ),
     *      @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            @OA\Property(
     *              property="data",
     *              description="ID партнёра",
     *              type="array",
     *              @OA\Items(
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="name", description="ФИО партнёра", type="object",
     *                     @OA\Property(property="ru", example="Иванов Иван Василевич")
     *                 ),
     *                 @OA\Property(property="link",description="Внешняя ссылка",
     *                    type="string", example="https://bankatering.ru/"),
     *                 @OA\Property(property="image",description="Изоброжение партнёра",type="string",example="http://newapiartrussiafair/api/v1/"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="deleted_at", type="string", example=null),
     *                 @OA\Property(property="important",
     *                      description="Флаг для тех,кто должен попасть на главную",type="boolean",example=false),
     *                 @OA\Property(property="partner_category", description="Категории партнёра",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer",example=1),
     *                     @OA\Property(property="name", type="object",
     *                         @OA\Property(property="ru", type="string", example="Партнеры"),
     *                     ),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="deleted_at", type="string", example=null),
     *                 ),
     *                 @OA\Property(property="eventgable", type="array",
     *                    @OA\Items(
     *                       @OA\Property(property="event_id", example=1, type="integer"),
     *                       @OA\Property(property="eventgable_type", example="App\\Models\\Partner", type="string"),
     *                       @OA\Property(property="eventgable_id", example=1, type="integer"),
     *                  ),
     *                ),
     *             ),
     *           ),
     *          @OA\Property(property="links", type="object",
     *              @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/partner/4"),
     *             ),
     *           ),
     *         ),
     *       ),
     *      @OA\Response(response=401,description="Unauthenticated"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404,description="Not found"),
     *      @OA\Response(response=403,description="Forbidden",
     *         @OA\JsonContent(
     *            @OA\Property(property="errors", type="array",
     *            @OA\Items(
     *               @OA\Property(property="status", example="403"),
     *               @OA\Property(property="detail", example="User does not have the right roles.")
     *            ),
     *         ),
     *      ),
     *    ),
     *    @OA\Response(response=500,description="Server error, not found")
     *    )
     *
     * @param PartnerRequest $request
     * @return PartnerCollection|ApiErrorResponse
     */
    public function list(PartnerRequest $request): PartnerCollection|ApiErrorResponse
    {
        $dataApp = $request->validated();
        try {
            return new PartnerCollection($this->partnerService->list($dataApp));
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при выводе списока партнёрыов', $e);
        }
    }
}
