<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\CategoryProduct\ListProductCategoryRequest;
use Admin\Http\Requests\CategoryProduct\StoreRequest;
use Admin\Http\Requests\CategoryProduct\UpdateRequest;
use Admin\Http\Resources\CategoryProduct\CategoryProductCollection;
use Admin\Http\Resources\CategoryProduct\CategoryProductResource;
use Admin\Services\CategoryProductService;
use Admin\Services\ParticipantService;
use App\Exceptions\CustomException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class CategoryProductController extends Controller
{
    private CategoryProductService $categoryProductService;

    public function __construct(CategoryProductService $categoryProductService)
    {
        $this->categoryProductService = $categoryProductService;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/category-products",
     *      operationId="createCategoryProduct",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Категория товаров"},
     *      summary="Создание новой категории товаров",
     *      @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *        type="object",
     *        required={"name","locate"},
     *          @OA\Property(property="name",description="Название категории товаров",type="object",
     *                 @OA\Property(property="ru", example="Тестовое название категории товаров"),
     *                 @OA\Property(property="en", example="Translate")
     *          ),
     *          @OA\Property(property="sort_id", description="ID сортировки", type="integer", example="1"),
     *          @OA\Property(property="locate",description="Язык", type="string", example="ru"),
     *       ),
     *     ),
     *     ),
     *
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     * @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     *
     */
    public function store(StoreRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $data = $request->validated();
            return new ApiSuccessResponse(
                new  CategoryProductResource($this->categoryProductService->create($data)),
                ['message' => 'Категория товаров успешно создана'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при создании категории товаров',
                $exception
            );
        }
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/admin/category-products/{id}",
     *      operationId="UpdateCategoryProduct",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Категория товаров"},
     *      summary="Редактирование категории товаров",
     *     @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *       @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *         mediaType="application/vnd.api+json",
     *         @OA\Schema(
     *         type="object",
     *         required={"locate"},
     *          @OA\Property(property="name",description="Название категории товаров",type="object",
     *             @OA\Property(property="ru", example="Тестовое название категории товаров"),
     *             @OA\Property(property="en", example="Translate")
     *          ),
     *         @OA\Property(property="sort_id", description="ID сортировки", type="integer", example="1"),
     *         @OA\Property(property="locate",description="Язык", type="string", example="ru"),
     *       ),
     *     ),
     *     ),
     *
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     * @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     *
     */
    public function update(int $id, UpdateRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $data = $request->validated();
            return new ApiSuccessResponse(
                new  CategoryProductResource($this->categoryProductService->update($id, $data)),
                ['message' => 'Категория товаров успешно обновлена'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при обновлении категории товаров',
                $exception
            );
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/admin/category-products/{id}",
     *      operationId="DelCategoryProduct",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Категория товаров"},
     *      summary="Удаление категории товаров",
     *     @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     * @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     *
     * @param int $id
     * @return CustomException|ApiSuccessResponse
     */
    public function destroy(int $id): CustomException|ApiSuccessResponse
    {
        try {
            return new ApiSuccessResponse(
                $this->categoryProductService->delete($id, ParticipantService::DELETE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException|CustomException $e) {
            return new CustomException($e);
        }
    }

    /**
     * @OA\Delete(
     *       path="/api/v1/admin/category-products/archive/{id}",
     *       operationId="AdminCategoryProductArchive",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Категория товаров"},
     *       summary="Добавить категорию товара в архив",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID cпикера",
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
     */
    public function archive(int $id): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            return new ApiSuccessResponse(
                $this->categoryProductService->delete($id, ParticipantService::ARCHIVE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException|CustomException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/category-products",
     *      operationId="CategoryCatalogAll",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Категория товаров"},
     *      summary="Получить список категорий товаров",
     *       @OA\Parameter(
     *           name="filter[trashed]",
     *           in="query",
     *           description="Показать удаленных(архивных) (with/only)",
     *           @OA\Schema(
     *              type="string",
     *              enum={"with","only"},
     *            )
     *        ),
     *        @OA\Parameter(
     *           name="filter[id]",
     *           in="query",
     *           description="ID категория товаров",
     *           @OA\Schema(
     *                 type="integer",
     *               )
     *         ),
     *       @OA\Parameter(
     *           name="filter[name]",
     *           in="query",
     *           description="Имя категория товаров",
     *           @OA\Schema(
     *              type="string",
     *           )
     *        ),
     *        @OA\Parameter(
     *           name="sort_by",
     *           in="query",
     *           description="Сортировка по поле",
     *           @OA\Schema(
     *              type="string",
     *              enum={"id","sort_id","name"},
     *           )
     *         ),
     *         @OA\Parameter(
     *            name="order_by",
     *            in="query",
     *            description="Порядок сортировки",
     *            @OA\Schema(
     *               type="string",
     *               enum={"ASC", "DESC"},
     *            )
     *         ),
     *        @OA\Parameter(
     *             name="page",
     *             in="query",
     *             description="Номер страницы",
     *             @OA\Schema(
     *                type="integer",
     *                example=1
     *            )
     *          ),
     *        @OA\Parameter(
     *            name="per_page",
     *            in="query",
     *            description="Количество элементов на странице",
     *            @OA\Schema(
     *               type="integer",
     *               example=10
     *           )
     *         ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *    @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     **/
    public function list(ListProductCategoryRequest $request): CategoryProductCollection|ApiErrorResponse
    {
        $dataApp = $request->validated();
        try {
            return new CategoryProductCollection($this->categoryProductService->list($dataApp));
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при выводе категори продуктов', $e);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/category-products/{id}",
     *      operationId="GetCategoryProductId",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Категория товаров"},
     *      summary="Просмотр категории товаров",
     *     @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     * @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     **/
    public function show(int $id): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            new  CategoryProductResource($this->categoryProductService->show($id)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Patch (
     *    path="/api/v1/admin/category-products/restore/{id}",
     *    operationId="RestoreCategoryProduct",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Категория товаров"},
     *    summary="Восстановить категория товаров из архива",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        @OA\Schema(
     *           type="integer"
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
     *               description="ID cпикера",
     *               type="object",
     *               @OA\Property(property="id", example="1", type="integer"),
     *               @OA\Property(property="type", example="speaker", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *                  @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *              @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                 @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                 @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *              ),
     *              @OA\Property(property="description", description="Описание художника", type="object",
     *                 @OA\Property(property="ru", example="Художник"),
     *                 @OA\Property(property="en", example="Artist")
     *              ),
     *                  @OA\Property(property="image",description="Изображение cпикера",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *                  @OA\Property(property="stand_id",description="Номер стенда",type="string",example="2a"),
     *                  @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                  @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                       @OA\Property(property="eventgable", type="array",
     *                       @OA\Items(
     *                           @OA\Property(property="event_id", type="integer", example="1"),
     *                           @OA\Property(property="eventgable_type",type="string",example="App\\Models\\speaker"),
     *                           @OA\Property(property="eventgable_id", type="integer", example="7"),
     *                       ),
     *                   ),
     *               ),
     *                @OA\Property(property="links", type="object",
     *                    @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/speaker/4"),
     *                 ),
     *               @OA\Property(property="relationships", type="object",
     *                  @OA\Property(property="events", type="object",
     *                    @OA\Property(property="data",type="object"),
     *                    @OA\Property(property="links",type="object",
     *                       @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/speaker/6/relationships/eventgable"),
     *                       @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/speaker/6/eventgable" ),
     *                   ),
     *                  ),
     *                ),
     *              ),
     *                  @OA\Property(property="metadata",type="object",
     *                     @OA\Property(property="message", example="Ok"),
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
     *                 @OA\Property(property="detail", example="Such an speaker does not exist in the archive."),
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
        if ($this->categoryProductService->checkData($id)) {
            $this->categoryProductService->restore($id);
            return new ApiSuccessResponse(
                new CategoryProductResource($this->categoryProductService->show($id)),
                ['message' => 'успешно восстановлен'],
                ResponseAlias::HTTP_OK
            );
        }
        return new ApiErrorResponse(
            'Such an speaker does not exist in the archive.',
            null,
            ResponseAlias::HTTP_NOT_FOUND
        );
    }
}
