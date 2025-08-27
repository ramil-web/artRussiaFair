<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\ServiceCatalog\ListServiceCatalogRequest;
use Admin\Http\Requests\ServiceCatalog\StoreServiceCatalogRequest;
use Admin\Http\Requests\ServiceCatalog\UpdateRequest;
use Admin\Http\Resources\Artist\ArtistResource;
use Admin\Http\Resources\ServiceCatalog\ServiceCatalogCollection;
use Admin\Http\Resources\ServiceCatalog\ServiceCatalogResource;
use Admin\Services\ParticipantService;
use Admin\Services\ServiceCatalogService;
use App\Exceptions\CustomException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class ServiceCatalogController extends Controller
{
    private ServiceCatalogService $serviceCatalogService;

    public function __construct(ServiceCatalogService $serviceCatalogService)
    {
        $this->serviceCatalogService = $serviceCatalogService;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/service-catalogs",
     *      operationId="createServiceCatalog",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Каталог услуг"},
     *      summary="Создание нового каталога услуг",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      @OA\Schema(
     *              type="object",
     *              required={"image","name","category","price","locate"},
     *              @OA\Property(property="image",description="Изображение", type="string",example="/upload/img2.jpg"),
     *              @OA\Property(property="sort_id", description="ID сортировки", type="integer", example="1"),
     *                   @OA\Property(property="name",description="Описание выставки",type="object",
     *                   @OA\Property(property="ru", example="Тестовое название услуги"),
     *                   @OA\Property(property="en", example="Test service")
     *               ),
     *              @OA\Property(property="description",description="Описание выставки",type="object",
     *                   @OA\Property(property="ru",example="Описание услуги"),
     *                   @OA\Property(property="en", example="Some text")
     *              ),
     *              @OA\Property(property="category",description="Категория",type="object",
     *                    @OA\Property(property="ru",example="Тестовая категория"),
     *                    @OA\Property(property="en", example="Some text")
     *             ),
     *             @OA\Property(property="other",description="Доп свойства",type="object",
     *                     @OA\Property(property="ru",example="Тест"),
     *                     @OA\Property(property="en", example="Some text")
     *              ),
     *             @OA\Property(property="price",description="Стоимость", type="integer", example="100"),
     *             @OA\Property(property="locate",description="Язык", type="string", example="ru"),
     *         ),
     *       ),
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
     */
    public function store(StoreServiceCatalogRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $data = $request->validated();
            return new ApiSuccessResponse(
                new  ServiceCatalogResource($this->serviceCatalogService->create($data)),
                ['message' => 'Каталог услуг успешно создан'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при создании каталога услуг',
                $exception
            );
        }
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/admin/service-catalogs/{id}",
     *      operationId="UpdateServiceCatalog",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Каталог услуг"},
     *      summary="Редактирование каталога услуг",
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
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *          type="object",
     *          required={"locate"},
     *          @OA\Property(property="image",description="Изображение", type="string",example="/upload/img2.jpg"),
     *          @OA\Property(property="sort_id", description="ID сортировки", type="integer", example="1"),
     *          @OA\Property(property="description",description="Описание выставки",type="object",
     *              @OA\Property(property="ru",example="Описание услуги"),
     *              @OA\Property(property="en", example="Some text")
     *          ),
     *          @OA\Property(property="category",description="Категория",type="object",
     *              @OA\Property(property="ru",example="Тестовая категория"),
     *              @OA\Property(property="en", example="Some text")
     *          ),
     *          @OA\Property(property="other",description="Доп свойства",type="object",
     *                    @OA\Property(property="ru",example="Тест"),
     *                    @OA\Property(property="en", example="Some text")
     *          ),
     *          @OA\Property(property="price",description="Стоимость", type="integer", example="100"),
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
    public function update(int $id, UpdateRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $data = $request->all();
            return new ApiSuccessResponse(
                new  ServiceCatalogResource($this->serviceCatalogService->update($id, $data)),
                ['message' => 'Каталог услуг успешно обновлен'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при обновлении каталога события',
                $exception
            );
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/service-catalogs",
     *      operationId="ServiceCatalogAll",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Каталог услуг"},
     *      summary="Получить список каталогов услуг",
     *       @OA\Parameter(
     *           name="filter[trashed]",
     *           in="query",
     *           description="Показать удаленных(архивных) (with/only)",
     *           @OA\Schema(
     *              type="string",
     *              enum={"with","only"},
     *            )
     *        ),
     *      @OA\Parameter(
     *          name="filter[name]",
     *          in="query",
     *          description="Фильтр по названию (мультиязычный)",
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="filter[category]",
     *          in="query",
     *          description="Фильтр категории (мультиязычный)",
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          description="Сортировка по поле",
     *          @OA\Schema(
     *             type="string",
     *             enum={"id","sort_id","name", "catalog", "other", "description","created_at", "updated_at"},
     *           )
     *       ),
     *       @OA\Parameter(
     *          name="order_by",
     *          in="query",
     *          description="Порядок сортировки",
     *          @OA\Schema(
     *             type="string",
     *             enum={"ASC", "DESC"},
     *           )
     *        ),
     *        @OA\Parameter(
     *              name="page",
     *              in="query",
     *              description="Номер страницы",
     *              @OA\Schema(
     *                 type="integer",
     *                 example=1
     *             )
     *         ),
     *        @OA\Parameter(
     *            name="per_page",
     *            in="query",
     *            description="Количество элементов на странице",
     *            @OA\Schema(
     *               type="integer",
     *                example=10
     *             )
     *        ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешно",
     *          @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Неаутентифицированный доступ"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Неверный запрос"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Запрещено"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Не найдено"
     *      )
     * )
     **/
    public function list(ListServiceCatalogRequest $request): ServiceCatalogCollection
    {
        $dataApp = $request->validated();
        return new ServiceCatalogCollection($this->serviceCatalogService->list($dataApp));
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/service-catalogs/{id}",
     *      operationId="GetServiceCatalogId",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Каталог услуг"},
     *      summary="Просмотр услуги",
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
            new  ServiceCatalogResource($this->serviceCatalogService->show($id)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/admin/service-catalogs/{id}",
     *      operationId="DelServiceCatalog",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Каталог услуг"},
     *      summary="Удаление каталога услуг",
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
    public function destroy(int $id): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            return new ApiSuccessResponse(
                $this->serviceCatalogService->delete($id, ParticipantService::DELETE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException|Throwable $e) {
            return new ResourceNotFoundException($e);
        }
    }


    /**
     * @OA\Delete(
     *       path="/api/v1/admin/service-catalogs/archive/{id}",
     *       operationId="ArchiveServiceCatalog",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Каталог услуг"},
     *       summary="Добавить каталог услуг в архив",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID каталог услуг",
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
     * @throws Throwable
     */
    public function archive(int $id): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            return new ApiSuccessResponse(
                $this->serviceCatalogService->delete($id, ParticipantService::ARCHIVE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Patch(
     *    path="/api/v1/admin/service-catalogs/restore/{id}",
     *    operationId="RestorerviceCatalog",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Каталог услуг"},
     *    summary="Восстановить каталог услуг из архива",
     *    @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       description="ID каталог услуг",
     *       @OA\Schema(
     *          type="integer",
     *          example="1",
     *       )
     *    ),
     *      @OA\Response(
     *           response=200,
     *           description="Успешно",
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
     *                 @OA\Property(property="detail", example="Such an artist does not exist in the archive."),
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
        if ($this->serviceCatalogService->checkData($id)) {
            $this->serviceCatalogService->restore($id);
            return new ApiSuccessResponse(
                new ArtistResource($this->serviceCatalogService->show($id)),
                ['message' => 'успешно восстановлен'],
                ResponseAlias::HTTP_OK
            );
        }
        return new ApiErrorResponse(
            'Such an artist does not exist in the archive.',
            null,
            ResponseAlias::HTTP_NOT_FOUND
        );
    }
}
