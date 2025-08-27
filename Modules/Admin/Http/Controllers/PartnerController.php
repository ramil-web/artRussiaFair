<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\Partner\ListPartnerRequest;
use Admin\Http\Requests\Partner\ShowPartnerRequest;
use Admin\Http\Requests\Partner\StorePartnerRequest;
use Admin\Http\Requests\Partner\UpdatePartnerRequest;
use Admin\Http\Resources\Partner\PartnerCollection;
use Admin\Http\Resources\Partner\PartnerResource;
use Admin\Services\PartnerService;
use App\Exceptions\CustomException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class PartnerController extends Controller
{

    public function __construct(protected PartnerService $partnerService)
    {
    }

    /**
     * @OA\Post(
     *    path="/api/v1/admin/partner/store",
     *    operationId="AdminPartnerSore",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Партнёры"},
     *    summary="Добавление партнёра",
     *    @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            required={"name", "partner_category_id"},
     *            @OA\Property(property="event_id", type="array",
     *               example={1},
     *               @OA\Items(
     *               type="integer"
     *               ),
     *            ),
     *            @OA\Property(property="sort_id", description="ID сортировки", type="integer", example="1"),
     *            @OA\Property(property="partner_category_id", description="ID категории партнёра", type="integer", example="1"),
     *            @OA\Property(property="important", description="Флаг для тех, кто должен попасть на главную", type="boolean", example=false),
     *             @OA\Property(property="name",description="ФИО партнёра",type="object",
     *                  @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                  @OA\Property(property="en", example="Ivan")
     *              ),
     *            @OA\Property(property="link",description="Внешняя ссылка",type="string", example="https://bankatering.ru/"),
     *            @OA\Property(property="image",description="Изоброжение партнёра",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *            @OA\Property(property="locate", description="Язык", type="string", example="ru"),
     *            ),
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
     *             description="ID партнёра",
     *             type="object",
     *             @OA\Property(property="id", example="1", type="integer"),
     *             @OA\Property(property="type", example="partner", type="string"),
     *                @OA\Property(property="attributes", type="object",
     *                @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                @OA\Property(property="important", description="Флаг для тех, кто должен попасть на главную", type="boolean", example=false),
     *             @OA\Property(property="name",description="ФИО партнёра",type="object",
     *                   @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                   @OA\Property(property="en", example="Ivan")
     *               ),
     *                @OA\Property(property="image",description="Изоброжение партнёра",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *                @OA\Property(property="link",description="Внешняя ссылка",type="string", example="https://bankatering.ru/"),
     *                @OA\Property(property="partner_category_id", description="ID категории партнёра", type="integer", example="1"),
     *                @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *             ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner/8"),
     *              ),
     *             @OA\Property(property="relationships", type="object",
     *                @OA\Property(property="events", type="object",
     *                  @OA\Property(property="data",type="object"),
     *                  @OA\Property(property="links",type="object",
     *                     @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner/6/relationships/eventgable"),
     *                     @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/partner/6/eventgable" ),
     *                 ),
     *                ),
     *                @OA\Property(property="partnerCategpry", type="object",
     *                     @OA\Property(property="data",type="object",
     *                        @OA\Property(property="id", type="integer", example=1),
     *                        @OA\Property(property="type", type="string", example="partner_categories"),
     *                       ),
     *                    @OA\Property(property="links",type="object",
     *                    @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner/34/relationships/partnerCategory"),
     *                    @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/partner/34/partnerCategory" ),
     *                    ),
     *                   ),
     *              ),
     *            ),
     *                @OA\Property(property="metadata",type="object",
     *                   @OA\Property(property="message", example="партнёры успешно добавлен"),
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
     * @param StorePartnerRequest $request
     * @return ApiErrorResponse|ApiSuccessResponse
     */
    public function store(StorePartnerRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $dataApp = $request->validated();
            return new ApiSuccessResponse(
                new  PartnerResource($this->partnerService->create($dataApp)),
                ['message' => 'Пратнёр успешно добавлен'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при добавление партнёра',
                $exception
            );
        }
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/partner/{id}",
     *    operationId="AdminGetPartner",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Партнёры"},
     *    summary="Получить данные партнёра",
     *    @OA\Parameter(
     *        name="filter[category]",
     *        in="query",
     *        description="Фильтр по category событие, пока modern|classic|etc",
     *        required=false,
     *        @OA\Schema(
     *           type="string",
     *           )
     *        ),
     *    @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       description="ID партнёра",
     *       @OA\Schema(
     *          type="integer",
     *          example="1",
     *           )
     *      ),
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
     *              type="object",
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="partner", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="important", description="Флаг для тех, кто должен попасть на главную", type="boolean", example=false),
     *             @OA\Property(property="name",description="ФИО партнёра",type="object",
     *                   @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                   @OA\Property(property="en", example="Ivan")
     *               ),
     *                 @OA\Property(property="image",description="Изображение партнёра",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *                 @OA\Property(property="link",description="Внешняя ссылка",type="string", example="https://bankatering.ru/"),
     *                 @OA\Property(property="partner_category_id", description="ID категории партнёра", type="integer", example="1"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="deleted_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="eventgable", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="event_id", type="integer", example="1"),
     *                          @OA\Property(property="eventgable_type", type="string", example="App\\Models\\partner"),
     *                          @OA\Property(property="eventgable_id", type="integer", example="7"),
     *                      ),
     *                  ),
     *                 @OA\Property(property="partner_category", type="object",
     *                     @OA\Property(property="id", type="integer", example="1"),
     *                     @OA\Property(property="name", type="object",
     *                         @OA\Property(property="ru", type="string", example="partner"),
     *                     ),
     *                     @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                     @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                     @OA\Property(property="deleted_at", type="string", example="2023-11-27 15:1"),
     *                     ),
     *               ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner/4"),
     *                ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner/6/relationships/eventgable"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/partner/6/eventgable" ),
     *                  ),
     *                 ),
     *                 @OA\Property(property="partnerCategpry", type="object",
     *                    @OA\Property(property="data",type="object",
     *                       @OA\Property(property="id", type="integer", example=1),
     *                       @OA\Property(property="type", type="string", example="partner_categories"),
     *                      ),
     *                   @OA\Property(property="links",type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner/34/relationships/partnerCategory"),
     *                   @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/partner/34/partnerCategory" ),
     *                   ),
     *                  ),
     *               ),
     *             ),
     *                 @OA\Property(property="metadata",type="object",
     *                    @OA\Property(property="message", example="Ok"),
     *                  ),
     *               ),
     *              ),
     *            ),
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
     * @param int $id
     * @param ShowPartnerRequest $request
     * @return ApiSuccessResponse
     */
    public function show(int $id, ShowPartnerRequest $request): ApiSuccessResponse
    {
        $data = $request->validated();
        return new ApiSuccessResponse(
            new  PartnerResource($this->partnerService->show($id, $data)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/partner/all",
     *    operationId="Admin\ApartPartnerList",
     *    tags={"Admin|Партнёры"},
     *    summary="Получить список партнёрыов",
     *    @OA\Parameter(
     *       name="filter[category]",
     *       in="query",
     *       description="Фильтр по category событие, пока modern|classic",
     *       required=false,
     *       @OA\Schema(
     *           type="string",
     *       )
     *     ),
     *    @OA\Parameter(
     *       name="filter[trashed]",
     *       in="query",
     *       description="Показать удаленных(архивных) (with/only)",
     *       @OA\Schema(
     *             type="string",
     *             enum={"with","only"},
     *           )
     *     ),
     *     @OA\Parameter(
     *        name="filter[event_id]",
     *        in="query",
     *        description="ID события",
     *        @OA\Schema(
     *             type="integer",
     *        )
     *     ),
     *    @OA\Parameter(
     *        name="filter[id]",
     *        in="query",
     *        description="ID партнёра",
     *        @OA\Schema(
     *            type="integer",
     *         )
     *     ),
     *    @OA\Parameter(
     *       name="filter[partner_category_id]",
     *       in="query",
     *       description="Фильтр по категории партнёра",
     *       @OA\Schema(
     *          type="integer",
     *        )
     *    ),
     *    @OA\Parameter(
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
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="partner", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="important", description="Флаг для тех, кто должен попасть на главную", type="boolean", example=false),
     *             @OA\Property(property="name",description="ФИО партнёра",type="object",
     *                   @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                   @OA\Property(property="en", example="Ivan")
     *               ),
     *                 @OA\Property(property="image",description="Изоброжение партнёра",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *                 @OA\Property(property="link",description="Внешняя ссылка",type="string", example="https://bankatering.ru/"),
     *                 @OA\Property(property="partner_category_id", description="ID категории партнёра", type="integer", example="1"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *              ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner/4"),
     *                ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner/6/relationships/eventgable"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/partner/6/eventgable" ),
     *                   ),
     *                ),
     *                @OA\Property(property="partnerCategpry", type="object",
     *                   @OA\Property(property="data",type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="type", type="string", example="partner_categories"),
     *                     ),
     *                  @OA\Property(property="links",type="object",
     *                  @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner/34/relationships/partnerCategory"),
     *                  @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/partner/34/partnerCategory" ),
     *                  ),
     *                 ),
     *               ),
     *             ),
     *           ),
     *           @OA\Property(property="links", type="object"),
     *           @OA\Property(property="meta",type="object",),
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
     * @param ListPartnerRequest $request
     * @return PartnerCollection|ApiErrorResponse
     */
    public function list(ListPartnerRequest $request): PartnerCollection|ApiErrorResponse
    {
        $dataApp = $request->validated();
        try {
            return new PartnerCollection($this->partnerService->list($dataApp));
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при выводе списока партнёрыов', $e);
        }
    }

    /**
     * @OA\Patch(
     *       path="/api/v1/admin/partner/update/{id}",
     *       operationId="AdminPartnerUpdate",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Партнёры"},
     *       summary="Редактирование данные партнёра",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID партнёра",
     *         @OA\Schema(
     *               type="integer",
     *               example="1",
     *           )
     *       ),
     *       @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *             type="object",
     *             required={"name", "description"},
     *             @OA\Property(property="event_id", type="array",
     *                example={1},
     *                @OA\Items(
     *                type="integer"
     *                ),
     *            ),
     *            @OA\Property(property="sort_id", description="ID сортировки", type="integer", example="1"),
     *            @OA\Property(property="important", description="Флаг для тех, кто должен попасть на главную", type="boolean", example=false),
     *             @OA\Property(property="name",description="ФИО партнёра",type="object",
     *                   @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                   @OA\Property(property="en", example="Ivan")
     *               ),
     *            @OA\Property(property="link",description="Внешняя ссылка",type="string", example="https://bankatering.ru/"),
     *            @OA\Property(property="partner_category_id", description="ID категории партнёра", type="integer", example="1"),
     *            @OA\Property(property="image",description="Изображение партнёра",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *            @OA\Property(property="locate", description="Язык", type="string", example="ru"),
     *            ),
     *          ),
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
     *              description="ID категории",
     *              type="object",
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="partner", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="important", description="Флаг для тех, кто должен попасть на главную", type="boolean", example=false),
     *             @OA\Property(property="name",description="ФИО партнёра",type="object",
     *                   @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                   @OA\Property(property="en", example="Ivan")
     *               ),
     *                 @OA\Property(property="image",description="Изображение партнёра",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *                 @OA\Property(property="link",description="Внешняя ссылка",type="string", example="https://bankatering.ru/"),
     *                 @OA\Property(property="partner_category_id", description="ID категории партнёра", type="integer", example="1"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="deleted_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="eventgable", type="array",
     *                    @OA\Items(
     *                       @OA\Property(property="event_id", type="integer", example="1"),
     *                       @OA\Property(property="eventgable_type", type="string", example="App\\Models\\partner"),
     *                       @OA\Property(property="eventgable_id", type="integer", example="7"),
     *                       ),
     *                   ),
     *                  @OA\Property(property="partner_category", type="object",
     *                      @OA\Property(property="id", type="integer", example="1"),
     *                      @OA\Property(property="name", type="object",
     *                          @OA\Property(property="ru", type="string", example="partner"),
     *                      ),
     *                      @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                      @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                      @OA\Property(property="deleted_at", type="string", example="2023-11-27 15:1"),
     *                      ),
     *                ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner/4"),
     *                ),
     *               @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                    @OA\Property(property="data",type="object"),
     *                    @OA\Property(property="links",type="object",
     *                       @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner/6/relationships/eventgable"),
     *                       @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/partner/6/eventgable" ),
     *                   ),
     *                 ),
     *               @OA\Property(property="partnerCategpry", type="object",
     *                    @OA\Property(property="data",type="object",
     *                       @OA\Property(property="id", type="integer", example=1),
     *                       @OA\Property(property="type", type="string", example="partner_categories"),
     *                      ),
     *                   @OA\Property(property="links",type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner/34/relationships/partnerCategory"),
     *                   @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/partner/34/partnerCategory" ),
     *                   ),
     *                  ),
     *               ),
     *             ),
     *                 @OA\Property(property="metadata",type="object",
     *                    @OA\Property(property="message", example="Ok"),
     *                  ),
     *               ),
     *              ),
     *            ),
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
     * @param int $id
     * @param UpdatePartnerRequest $request
     * @return ApiSuccessResponse|ResourceNotFoundException
     * @throws CustomException
     * @throws Throwable
     */
    public function update(int $id, UpdatePartnerRequest $request): ApiSuccessResponse|ResourceNotFoundException
    {
        $dataApp = $request->validated();
        try {
            return new ApiSuccessResponse(
                new PartnerResource($this->partnerService->update($id, $dataApp)),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Delete(
     *       path="/api/v1/admin/partner/delete/{id}",
     *       operationId="AdminPartnerDelete",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Партнёры"},
     *       summary="Полностью удаление партнёра",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID партнёра",
     *         @OA\Schema(
     *               type="integer",
     *               example="1",
     *           )
     *       ),
     *      @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *              @OA\Property(property="data", type="Fboolean", example="true"),
     *              @OA\Property(property="metadata",type="object",
     *                 @OA\Property(property="message", example="Ok"),
     *              ),
     *            ),
     *         ),
     *     ),
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
     * @param int $id
     * @return ResourceNotFoundException|ApiSuccessResponse
     * @throws CustomException
     */
    public function delete(int $id): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            return new ApiSuccessResponse(
                $this->partnerService->delete($id, partnerService::DELETE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Delete(
     *       path="/api/v1/admin/partner/archive/{id}",
     *       operationId="AdminPartnerArchive",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Партнёры"},
     *       summary="Добавить партнёра в архив",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID партнёра",
     *         @OA\Schema(
     *               type="integer",
     *               example="1",
     *           )
     *       ),
     *      @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *              @OA\Property(property="data", type="boolean", example="true"),
     *              @OA\Property(property="metadata",type="object",
     *                 @OA\Property(property="message", example="Ok"),
     *              ),
     *            ),
     *         ),
     *     ),
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
     * @param int $id
     * @return ResourceNotFoundException|ApiSuccessResponse
     * @throws CustomException
     */
    public function archive(int $id): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            return new ApiSuccessResponse(
                $this->partnerService->delete($id, partnerService::ARCHIVE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Patch(
     *    path="/api/v1/admin/partner/restore/{id}",
     *    operationId="RestorePartner",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Партнёры"},
     *    summary="Восстановить партнёра из архива",
     *    @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       description="ID партнёра",
     *       @OA\Schema(
     *          type="integer",
     *          example="1",
     *       )
     *    ),
     *      @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *               property="data",
     *               description="ID партнёра",
     *               type="object",
     *               @OA\Property(property="id", example="1", type="integer"),
     *               @OA\Property(property="type", example="partner", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *                  @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *               @OA\Property(property="name",description="ФИО партнёра",type="object",
     *                   @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                   @OA\Property(property="en", example="Ivan")
     *                ),
     *                  @OA\Property(property="image",description="Изображение партнёра",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *                  @OA\Property(property="link",description="Внешняя ссылка",type="string", example="https://bankatering.ru/"),
     *                  @OA\Property(property="partner_category_id", description="ID категории партнёра", type="integer", example="1"),
     *                  @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                  @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                  @OA\Property(property="deleted_at", type="string", example="2023-11-27 15:1"),
     *                  @OA\Property(property="eventgable", type="array",
     *                       @OA\Items(
     *                           @OA\Property(property="event_id", type="integer", example="1"),
     *                           @OA\Property(property="eventgable_type", type="string", example="App\\Models\\partner"),
     *                           @OA\Property(property="eventgable_id", type="integer", example="7"),
     *                       ),
     *                   ),
     *                  @OA\Property(property="partner_category", type="object",
     *                      @OA\Property(property="id", type="integer", example="1"),
     *                      @OA\Property(property="name", type="object",
     *                          @OA\Property(property="ru", type="string", example="partner"),
     *                      ),
     *                      @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                      @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                      @OA\Property(property="deleted_at", type="string", example="2023-11-27 15:1"),
     *                      ),
     *               ),
     *                @OA\Property(property="links", type="object",
     *                    @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner/4"),
     *                 ),
     *               @OA\Property(property="relationships", type="object",
     *                  @OA\Property(property="events", type="object",
     *                    @OA\Property(property="data",type="object"),
     *                    @OA\Property(property="links",type="object",
     *                       @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner/6/relationships/eventgable"),
     *                       @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/partner/6/eventgable" ),
     *                   ),
     *                  ),
     *                 @OA\Property(property="partnerCategpry", type="object",
     *                    @OA\Property(property="data",type="object",
     *                       @OA\Property(property="id", type="integer", example=1),
     *                       @OA\Property(property="type", type="string", example="partner_categories"),
     *                      ),
     *                   @OA\Property(property="links",type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner/34/relationships/partnerCategory"),
     *                   @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/partner/34/partnerCategory" ),
     *                   ),
     *                  ),
     *                ),
     *              ),
     *                  @OA\Property(property="metadata",type="object",
     *                     @OA\Property(property="message", example="Успешно восстановлен"),
     *                   ),
     *                ),
     *               ),
     *             ),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=400, description="Bad Request"),
     *       @OA\Response(response=404,description="Not Found",
     *           @OA\JsonContent(
     *              @OA\Property(property="errors", type="array",
     *              @OA\Items(
     *                 @OA\Property(property="status", example="404"),
     *                 @OA\Property(property="detail", example="Such an partner does not exist in the archive."),
     *              ),
     *           ),
     *        ),
     *      ),
     *       @OA\Response(response=403,description="Forbidden",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", example="User does not have the right roles.")
     *        ),
     *     ),
     *     @OA\Response(response=500,description="Server error, not found")
     *     ),
     *   ),
     * @param int $id
     * @return ApiSuccessResponse|ApiErrorResponse
     * @throws CustomException
     **/
    public function restore(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        if ($this->partnerService->checkData($id)) {
            $this->partnerService->restore($id);
            return new ApiSuccessResponse(
                new PartnerResource($this->partnerService->show($id)),
                ['message' => 'Успешно восстановлен'],
                ResponseAlias::HTTP_OK
            );
        }
        return new ApiErrorResponse(
            'Such an partner does not exist in the archive.',
            null,
            ResponseAlias::HTTP_NOT_FOUND
        );
    }
}

