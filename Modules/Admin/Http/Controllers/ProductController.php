<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\Product\ListProductRequest;
use Admin\Http\Requests\Product\ShowProductRequest;
use Admin\Http\Requests\Product\StoreRequest;
use Admin\Http\Requests\Product\UpdateRequest;
use Admin\Http\Resources\Product\ProductCollection;
use Admin\Http\Resources\Product\ProductResource;
use Admin\Services\ParticipantService;
use Admin\Services\ProductService;
use App\Exceptions\CustomException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/products",
     *      operationId="createProduct",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Товар"},
     *      summary="Создание нового товара",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            required={"name","description","price","locate","category_id","article"},
     *            @OA\Property(property="category_product_id", description="ID категории", type="integer", example="1"),
     *            @OA\Property(property="sort_id", description="ID сортировки", type="integer", example="1"),
     *            @OA\Property(property="name",description="Название товара",type="object",
     *                 @OA\Property(property="ru", example="Стол"),
     *                 @OA\Property(property="en", example="table")
     *             ),
     *             @OA\Property(property="description",description="Описание товара",type="object",
     *                 @OA\Property(property="ru",example="писменный стол"),
     *                 @OA\Property(property="en", example="Desk")
     *             ),
     *            @OA\Property(property="price", description="Цена товара", type="integer", example="100"),
     *            @OA\Property(property="image_path", description="Путь к изображению", type="string", example="uploads/product_images/TMrLiSDPIIgDu0rxsYhx.png"),
     *            @OA\Property(property="locate", description="Язык", type="string", example="ru"),
     *            @OA\Property(property="specifications", description="Характеристики", type="object",
     *                @OA\Property(property="ru", type="object",
     *                   @OA\Property(property="width", description="Ширина", type="integer", example="10"),
     *                   @OA\Property(property="height", description="Высота", type="integer", example="20"),
     *                   @OA\Property(property="depth", description="Глубина", type="integer", example="5")
     *               ),
     *            ),
     *            @OA\Property(property="article", description="Артикул товара", type="string", example="123456"),
     *          ),
     *        ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function store(StoreRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $data = $request->validated();
            return new ApiSuccessResponse(
                new  ProductResource($this->productService->create($data)),
                ['message' => 'Товар успешно создан'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при создании товара',
                $exception
            );
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/products",
     *     operationId="AdminProduct",
     *     security={{"bearerAuth":{}}},
     *     tags={"Admin|Товар"},
     *     summary="Просмотр товара",
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
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
     * @throws CustomException
     */
    public function show(ShowProductRequest $request): ApiSuccessResponse
    {
        $dataApp = $request->validated();
        return new ApiSuccessResponse(
            new  ProductResource($this->productService->show($dataApp['id'])),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/products/all",
     *    operationId="AdminProductAll",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Товар"},
     *    summary="Получить список товаров",
     *    @OA\Parameter(
     *       name="filter[trashed]",
     *       in="query",
     *       description="Показать удаленных(архивных) (with/only)",
     *       @OA\Schema(
     *          type="string",
     *          enum={"with","only"},
     *         )
     *      ),
     *      @OA\Parameter(
     *          name="filter[specifications]",
     *          in="query",
     *          description="{Фильтр по арактеристикам}",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="filter[category_product_id]",
     *          in="query",
     *          description="Фильтр по категории",
     *          @OA\Schema(
     *              type="integer",
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="filter[article]",
     *          in="query",
     *          description="Фильтр по артикулу товара",
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *        @OA\Parameter(
     *            name="sort_by",
     *            in="query",
     *            description="Сортировка по поле",
     *            @OA\Schema(
     *               type="string",
     *               enum={"id","sort_id","name"},
     *            )
     *          ),
     *          @OA\Parameter(
     *             name="order_by",
     *             in="query",
     *             description="Порядок сортировки",
     *             @OA\Schema(
     *                type="string",
     *                enum={"ASC", "DESC"},
     *             )
     *          ),
     *         @OA\Parameter(
     *              name="page",
     *              in="query",
     *              description="Номер страницы",
     *              @OA\Schema(
     *                 type="integer",
     *                 example=1
     *             )
     *           ),
     *         @OA\Parameter(
     *             name="per_page",
     *             in="query",
     *             description="Количество элементов на странице",
     *             @OA\Schema(
     *                type="integer",
     *                example=10
     *            )
     *          ),
     *   @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *      description="Unauthenticated"
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
    public function list(ListProductRequest $request): ProductCollection
    {
        $dataApp = $request->validated();
        return new ProductCollection($this->productService->list($dataApp));
    }


    /**
     * @OA\Patch(
     *      path="/api/v1/admin/products/{id}",
     *      operationId="UpdateProduct",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Товар"},
     *      summary="Редактирование товара",
     *      @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        @OA\Schema(
     *           type="integer"
     *        )
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *             type="object",
     *             @OA\Property(property="category_product_id",description="ID категории", type="integer", example="1"),
     *             @OA\Property(property="sort_id", description="ID сортировки", type="integer", example="1"),
     *             @OA\Property(property="name",description="Название товара",type="object",
     *                  @OA\Property(property="ru", example="Стол"),
     *                  @OA\Property(property="en", example="table")
     *              ),
     *              @OA\Property(property="description",description="Описание товара",type="object",
     *                  @OA\Property(property="ru",example="писменный стол"),
     *                  @OA\Property(property="en", example="Desk")
     *              ),
     *             @OA\Property(property="price", description="Цена товара", type="integer", example="100"),
     *             @OA\Property(property="locate", description="Язык", type="string", example="ru"),
     *             @OA\Property(property="image_path", description="Путь к изображению", type="string", example="uploads/product_images/TMrLiSDPIIgDu0rxsYhx.png"),
     *             @OA\Property(property="specifications", description="Характеристики", type="object",
     *                 @OA\Property(property="ru", type="object",
     *                    @OA\Property(property="width", description="Ширина", type="integer", example="10"),
     *                    @OA\Property(property="height", description="Высота", type="integer", example="20"),
     *                    @OA\Property(property="depth", description="Глубина", type="integer", example="5")
     *                ),
     *             ),
     *             @OA\Property(property="article", description="Артикул товара", type="string", example="123456"),
     *           ),
     *         ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function update(int $id, UpdateRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $data = $request->all();
            return new ApiSuccessResponse(
                new  ProductResource($this->productService->update($id, $data)),
                ['message' => 'Товар успешно обновлен'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при обновлении товара',
                $exception
            );
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/admin/products/{id}",
     *      operationId="DelProduct",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Товар"},
     *      summary="Удаление товара",
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
     * @throws CustomException
     */
    public function destroy(int $id): ApiSuccessResponse
    {
        $response = $this->productService->delete($id, ParticipantService::DELETE);
        return new ApiSuccessResponse(
            $response,
            ['message' => 'Товар успешно удален'],
            ResponseAlias::HTTP_NO_CONTENT
        );
    }

    /**
     * @OA\Delete(
     *    path="/api/v1/admin/products/archive/{id}",
     *    operationId="AdminProductArchive",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Товар"},
     *    summary="Добавить продукт в архив",
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID продукта",
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
                $this->productService->delete($id, ParticipantService::ARCHIVE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Patch(
     *    path="/api/v1/admin/products/restore/{id}",
     *    operationId="RestoreProduct",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Товар"},
     *    summary="Восстановить продукта из архива",
     *    @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       description="ID продукта",
     *       @OA\Schema(
     *          type="integer",
     *          example="1",
     *       )
     *    ),
     *      @OA\Response(
     *           response=200,
     *           description="Success",
     *           @OA\MediaType(
     *               mediaType="application/vnd.api+json",
     *           )
     *       ),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=400, description="Bad Request"),
     *       @OA\Response(response=404,description="Not Found",
     *           @OA\JsonContent(
     *              @OA\Property(property="errors", type="array",
     *              @OA\Items(
     *                 @OA\Property(property="status", example="404"),
     *                 @OA\Property(property="detail", example="Such an product does not exist in the archive."),
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
     *
     * @throws CustomException
     */
    public function restore(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        if ($this->productService->checkData($id)) {
            $this->productService->restore($id);
            return new ApiSuccessResponse(
                new productResource($this->productService->show($id)),
                ['message' => 'успешно восстановлен'],
                ResponseAlias::HTTP_OK
            );
        }
        return new ApiErrorResponse(
            'Such an product does not exist in the archive.',
            null,
            ResponseAlias::HTTP_NOT_FOUND
        );
    }
}
