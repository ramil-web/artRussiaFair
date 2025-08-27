<?php

namespace App\Http\Controllers;


use App\Http\Requests\PartnerCategory\ListPartnerCategoryRequest;
use App\Http\Resources\Partner\PartnerCollection;
use App\Http\Resources\PartnerCategory\PartnerCategoryCollection;
use App\Http\Responses\ApiErrorResponse;
use App\Services\PartnerCategoryService;
use OpenApi\Annotations as OA;
use Throwable;

class PartnerCategoryController extends Controller
{
    public function __construct(protected PartnerCategoryService $partnerCategoryService)
    {
    }

    /**
     * @OA\Get(
     *       path="/api/v1/partner-categories",
     *       operationId="AppartnerCategoryList",
     *       security={{"bearerAuth":{}}},
     *       tags={"App|Категория партнёра"},
     *       summary="Получить список категории партнёрыов",
     *       @OA\Parameter(
     *          name="filter[id]",
     *          in="query",
     *          description="ID категории партнёра",
     *          @OA\Schema(
     *             type="integer",
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="filter[name]",
     *         in="query",
     *         description="Фильтр по названию категории партнёра",
     *         @OA\Schema(
     *            type="string",
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Сортировка по поле",
     *         @OA\Schema(
     *            type="string",
     *            enum={"id","sort_id","name","created_at", "updated_at"},
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
     *                type="integer"
     *            )
     *        ),
     *       @OA\Parameter(
     *           name="per_page",
     *           in="query",
     *           description="Количество элементов на странице",
     *           @OA\Schema(
     *              type="integer"
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
     *              description="ID категории партнёра",
     *              type="array",
     *              @OA\Items(
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="partner", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="name", description="Название категории партнёра", type="object",
     *                     @OA\Property(property="ru", example="Партнёр")
     *                 ),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="sort_id",description="Идентификатор для сортировки",type="integer",example="1"),
     *              ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/partner-category/4"),
     *                ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/partner-category/6/relationships/eventgable"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/partner-category/6/eventgable" ),
     *                   ),
     *                 ),
     *                 @OA\Property(property="partnerCategpry", type="object",
     *                    @OA\Property(property="data",type="object"),
     *                    @OA\Property(property="links",type="object",
     *                    @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/partner/34/relationships/partnerCategory"),
     *                    @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/partner/34/partnerCategory" ),
     *                    ),
     *                   ),
     *               ),
     *             ),
     *           ),
     *           @OA\Property(property="links", type="object",
     *              @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/partner/all"),
     *            ),
     *           ),
     *          ),
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
     * @param ListPartnerCategoryRequest $request
     * @return PartnerCollection|ApiErrorResponse
     */
    public function list(ListPartnerCategoryRequest $request): PartnerCategoryCollection|ApiErrorResponse
    {
        $dataApp = $request->validated();
        try {
            return new PartnerCategoryCollection($this->partnerCategoryService->list($dataApp));
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при выводе списока категории партнёрыов', $e);
        }
    }
}
