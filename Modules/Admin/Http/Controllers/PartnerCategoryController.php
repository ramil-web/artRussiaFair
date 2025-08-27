<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\PartnerCategory\ListPartnerCategoryRequest;
use Admin\Http\Requests\PartnerCategory\StorePartnerCategoryRequest;
use Admin\Http\Requests\PartnerCategory\UpdatePartnerCategoryRequest;
use Admin\Http\Resources\Partner\PartnerResource;
use Admin\Http\Resources\PartnerCategory\PartnerCategoryCollection;
use Admin\Http\Resources\PartnerCategory\PartnerCategoryResource;
use Admin\Services\PartnerCategoryService;
use Admin\Services\PartnerService;
use App\Exceptions\CustomException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class PartnerCategoryController extends Controller
{
    public function __construct(protected PartnerCategoryService $partnerCategoryService)
    {
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/partner-category/store",
     *      operationId="AdminPartnerCategorySore",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Категория партнёра"},
     *      summary="Добавление категории партнёра",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            required={"name", "sort_id"},
     *            @OA\Property(property="sort_id", description="ID сортировки", type="integer", example="1"),
     *            @OA\Property(property="name",description="Название категории партнера",type="object",
     *                 @OA\Property(property="ru", example="Партнеры"),
     *                 @OA\Property(property="en", example="Partners")
     *             ),
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
     *             description="ID Категории категории партнёра",
     *             type="object",
     *             @OA\Property(property="id", example="1", type="integer"),
     *             @OA\Property(property="type", example="partner-category", type="string"),
     *                @OA\Property(property="attributes", type="object",
     *                @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *             @OA\Property(property="name",description="Название категории партнера",type="object",
     *                  @OA\Property(property="ru", example="Партнеры"),
     *                  @OA\Property(property="en", example="Partners")
     *              ),
     *                @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                @OA\Property(property="sort_id",description="Идентификатор для сортировки",type="integer",example="1"),
     *             ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner-category-category/8"),
     *              ),
     *             @OA\Property(property="relationships", type="object",
     *                @OA\Property(property="events", type="object",
     *                  @OA\Property(property="data",type="object"),
     *                  @OA\Property(property="links",type="object",
     *                     @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner-category-category/6/relationships/eventgable"),
     *                     @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/partner-category-category/6/eventgable" ),
     *                 ),
     *                ),
     *              ),
     *            ),
     *                @OA\Property(property="metadata",type="object",
     *                   @OA\Property(property="message", example="Категория категории партнёра успешно добавлен"),
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
     * @param StorePartnerCategoryRequest $request
     * @return ApiErrorResponse|ApiSuccessResponse
     */
    public function store(StorePartnerCategoryRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $dataApp = $request->validated();
            return new ApiSuccessResponse(
                new  PartnerCategoryResource($this->partnerCategoryService->create($dataApp)),
                ['message' => 'Категория пратнёр успешно добавлен'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при добавление категории категории партнёра',
                $exception
            );
        }
    }

    /**
     * @OA\Get(
     *       path="/api/v1/admin/partner-category/{id}",
     *       operationId="AdminGetPartnerCategory",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Категория партнёра"},
     *       summary="Получить данные категории партнёра",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID категории категории партнёра",
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
     *            type="object",
     *            @OA\Property(
     *              property="data",
     *              description="ID категории партнёра",
     *              type="object",
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="partner", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *              @OA\Property(property="name",description="Название категории партнера",type="object",
     *                   @OA\Property(property="ru", example="Партнеры"),
     *                   @OA\Property(property="en", example="Partners")
     *               ),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="sort_id", description="Идентификатор для сортировки", type="integer", example="1"),
     *               ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner-category-category/4"),
     *                ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner-category-category/6/relationships/eventgable"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/partner-category-category/6/eventgable" ),
     *                  ),
     *                 ),
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
     * @return ApiSuccessResponse
     */
    public function show(int $id): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            new  PartnerResource($this->partnerCategoryService->show($id)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *       path="/api/v1/admin/partner-category/all",
     *       operationId="AdminPartnerCategoryList",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Категория партнёра"},
     *       summary="Получить список категории партнёрыов",
     *       @OA\Parameter(
     *          name="filter[id]",
     *          in="query",
     *          description="ID категории партнёра",
     *          @OA\Schema(
     *             type="integer",
     *         )
     *      ),
     *     @OA\Parameter(
     *           name="filter[trashed]",
     *           in="query",
     *           description="Показать удаленных(архивных) (with/only)",
     *           @OA\Schema(
     *              type="string",
     *              enum={"with","only"},
     *            )
     *        ),
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
     *             @OA\Property(property="name",description="Название категории партнера",type="object",
     *                   @OA\Property(property="ru", example="Партнеры"),
     *                   @OA\Property(property="en", example="Partners")
     *               ),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="sort_id", description="Идентификатор для сортировки", type="integer", example="1"),
     *              ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner-category/4"),
     *                ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner-category/6/relationships/eventgable"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/partner-category/6/eventgable" ),
     *                   ),
     *                 ),
     *                 @OA\Property(property="partnerCategpry", type="object",
     *                    @OA\Property(property="data",type="object"),
     *                    @OA\Property(property="links",type="object",
     *                    @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner/34/relationships/partnerCategory"),
     *                    @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/partner/34/partnerCategory" ),
     *                    ),
     *                   ),
     *               ),
     *             ),
     *           ),
     *           @OA\Property(property="links", type="object",
     *              @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/admin/partner/all"),
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
     * @return PartnerCategoryCollection|ApiErrorResponse
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

    /**
     * @OA\Patch(
     *       path="/api/v1/admin/partner-category/update/{id}",
     *       operationId="AdminPartnerCategoryUpdate",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Категория партнёра"},
     *       summary="Редактирование данные категории партнёра",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID категории партнёра",
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
     *             @OA\Property(property="name",description="Название категории партнера",type="object",
     *                   @OA\Property(property="ru", example="Партнеры"),
     *                   @OA\Property(property="en", example="Partners")
     *               ),
     *            @OA\Property(property="locate", description="Язык", type="string", example="ru"),
     *            @OA\Property(property="sort_id", description="ID сортировки", type="integer", example=1),
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
     *              @OA\Property(property="name",description="Название категории партнера",type="object",
     *                   @OA\Property(property="ru", example="Партнеры"),
     *                   @OA\Property(property="en", example="Partners")
     *               ),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="sort_id", description="Идентификатор для сортировки", type="integer", example="1"),
     *              ),
     *              @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner-category/4"),
     *                ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                    @OA\Property(property="data",type="object"),
     *                    @OA\Property(property="links",type="object",
     *                       @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/partner-category/6/relationships/eventgable"),
     *                       @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/partner-category/6/eventgable" ),
     *                  ),
     *                 ),
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
     * @param UpdatePartnerCategoryRequest $request
     * @return ApiSuccessResponse|ResourceNotFoundException
     * @throws CustomException
     */
    public function update(int $id, UpdatePartnerCategoryRequest $request): ApiSuccessResponse|ResourceNotFoundException
    {
        $dataApp = $request->validated();

        try {
            return new ApiSuccessResponse(
                new PartnerResource($this->partnerCategoryService->update($id, $dataApp)),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Delete(
     *       path="/api/v1/admin/partner-category/delete/{id}",
     *       operationId="AdminPartnerCategoryDelete",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Категория партнёра"},
     *       summary="Полностью удаление категории партнёра",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID категории партнёра",
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
    public function delete(int $id): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            return new ApiSuccessResponse(
                $this->partnerCategoryService->delete($id, PartnerService::DELETE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Delete(
     *       path="/api/v1/admin/partner-category/archive/{id}",
     *       operationId="AdminPartnerCategoryArchive",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Категория партнёра"},
     *       summary="Добавить категорию партнёра в архив",
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
                $this->partnerCategoryService->delete($id, partnerService::ARCHIVE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }
    /**
     * @OA\Patch(
     *    path="/api/v1/admin/partner-category/restore/{id}",
     *    operationId="RestorePartnerCategory",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Категория партнёра"},
     *    summary="Восстановить категорию партнёра из архива",
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
        if ($this->partnerCategoryService->checkData($id)) {
            $this->partnerCategoryService->restore($id);
            return new ApiSuccessResponse(
                new PartnerResource($this->partnerCategoryService->show($id)),
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
