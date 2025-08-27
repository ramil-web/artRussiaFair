<?php

namespace Lk\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\StorageService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Lk\Http\Requests\Visualization\VisualisationListRequest;
use Lk\Http\Requests\Visualization\VisualizationImageDeleteRequest;
use Lk\Http\Requests\Visualization\VisualizationShowRequest;
use Lk\Http\Requests\Visualization\VisualizationStoreRequest;
use Lk\Http\Requests\Visualization\VisualizationUpdateRequest;
use Lk\Http\Resources\Visualization\VisualizationApplicationCollection;
use Lk\Http\Resources\Visualization\VisualizationApplicationResource;
use Lk\Services\AppVisualizationService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Throwable;

class VisualizationController extends Controller
{

    public function __construct(
        protected StorageService          $storageService,
        protected AppVisualizationService $appVisualization
    ) {
    }

    /**
     * @OA\Post(
     *      path="/api/v1/lk/applications/visualization/store",
     *      tags={"Lk|Визуализация"},
     *      security={{"bearerAuth":{}}},
     *      summary="Создает визуализации",
     *      operationId="lkCreatewVisualization",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/vnd.api+json",
     *             @OA\Schema(
     *                 @OA\Property(property="user_application_id", type="integer",example=1),
     *                 @OA\Property(property="url",description="Картинки художника",type="array",
     *                    example={
     *                      "http://newapiartrussiafair/api/v1/artist/nature.jpg",
     *                      "http://newapiartrussiafair/api/v1/artist/natureww.jpg"
     *                   },
     *                   @OA\Items(type="string"),
     *                 ),
     *             ),
     *          ),
     *     ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *              @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     *
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
     *      ),
     *    @OA\Response(response=409,description="Conflict, alredy existed",
     *       @OA\JsonContent(
     *          @OA\Property(property="errors", type="array",
     *             @OA\Items(
     *                @OA\Property(property="status", example="409"),
     *                @OA\Property(property="detail", example="Для этой заявки, моя команда уже существует!")
     *             ),
     *         ),
     *       ),
     *    ),
     *)
     * @param VisualizationStoreRequest $request
     * @return ApiSuccessResponse|JsonResponse
     * @throws CustomException
     * @throws Throwable
     */
    public function store(VisualizationStoreRequest $request): ApiSuccessResponse|JsonResponse
    {
        $appData = $request->validated();

        /**
         * Проверяем есть ли команда привязанная к этой заявке
         */
        if ($this->appVisualization->checkVisualisation($appData['user_application_id'])) {
            return response()->json([
                'errors' => [
                    [
                        'status' => ResponseAlias::HTTP_CONFLICT,
                        'detail' => 'Для этой заявки, визуализация уже существует!',
                    ],
                ],
            ], ResponseAlias::HTTP_CONFLICT);
        }
        return new ApiSuccessResponse(
            new VisualizationApplicationResource($this->appVisualization->create($appData)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *      path="/api/v1/lk/applications/visualization/show",
     *      tags={"Lk|Визуализация"},
     *      security={{"bearerAuth":{}}},
     *      summary="Получит визуализацию",
     *      operationId="lkGetVisualization",
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          required=true,
     *         description="id заявки",
     *
     *        @OA\Schema(
     *             type="integer"
     *        )
     *     ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *
     *              @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     *
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
     *)
     * @param VisualizationShowRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function show(VisualizationShowRequest $request): ApiSuccessResponse
    {
        $appData = $request->validated();
        return new ApiSuccessResponse(
            new VisualizationApplicationResource($this->appVisualization->show($appData['id'])),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *      path="/api/v1/lk/applications/visualization/list",
     *      tags={"Lk|Визуализация"},
     *      security={{"bearerAuth":{}}},
     *      summary="Получит список визуализации",
     *      operationId="lkGetVisualizations",
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="ID заявки",
     *
     *        @OA\Schema(
     *             type="integer"
     *        )
     *     ),
     *     @OA\Parameter(
     *         name="filter[trashed]",
     *         in="query",
     *         description="Показать удаленных(архивных) (with/only)",
     *         @OA\Schema(
     *            type="string",
     *             enum={"with","only"},
     *           )
     *        ),
     *        @OA\Parameter(
     *           name="filter[user_application_id]",
     *           in="query",
     *           description="ID художника",
     *           @OA\Schema(
     *              type="integer",
     *          )
     *       ),
     *       @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          description="Сортировка по поле",
     *          @OA\Schema(
     *             type="string",
     *             enum={"id","created_at", "updated_at"},
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
     *     @OA\Response(
     *      response=200,
     *       description="Success",
     *              @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     *
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
     *)
     * @param VisualisationListRequest $request
     * @return ApiSuccessResponse
     */
    public function list(VisualisationListRequest $request): ApiSuccessResponse
    {
        $dataApp = $request->validated();
        return new ApiSuccessResponse(
            new VisualizationApplicationCollection($this->appVisualization->list($dataApp)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }


    /**
     * @OA\Patch(
     *       path="/api/v1/lk/applications/visualization/update",
     *       operationId="LKAppVisualizationUpdate",
     *       security={{"bearerAuth":{}}},
     *       tags={"Lk|Визуализация"},
     *       summary="Редактирование Визуализации",
     *       @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="ID визуализации",
     *         @OA\Schema(
     *               type="integer",
     *               example="1",
     *           )
     *       ),
     *       @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *              @OA\Schema(
     *                  @OA\Property(property="user_application_id", type="integer",example=1),
     *                  @OA\Property(property="url",description="Картинки художника",type="array",
     *                     example={
     *                       "http://newapiartrussiafair/api/v1/artist/nature.jpg",
     *                       "http://newapiartrussiafair/api/v1/artist/natureww.jpg"
     *                    },
     *                    @OA\Items(type="string"),
     *                  ),
     *              ),
     *           ),
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
     *              description="ID категории",
     *              type="object",
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="artist", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                     @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                     @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *                 ),
     *                 @OA\Property(property="description", description="Описание художника", type="object",
     *                     @OA\Property(property="ru", example="Художник"),
     *                     @OA\Property(property="en", example="Artist")
     *                 ),
     *                 @OA\Property(property="image",description="Изображение художника",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *                 @OA\Property(property="stand_id",description="Номер стенда",type="string",example="2a"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="deleted_at", type="string", example=null),
     *                 @OA\Property(property="slug", type="string", example="artist_1"),
     *                 @OA\Property(property="images",description="Картинки художника",type="array",
     *                    example={
     *                      "http://newapiartrussiafair/api/v1/artist/nature.jpg",
     *                      "http://newapiartrussiafair/api/v1/artist/natureww.jpg"
     *                   },
     *                   @OA\Items(
     *                  type="string"
     *                  ),
     *                ),
     *              ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/artist/4"),
     *                ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/artist/6/relationships/eventgable"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/artist/6/eventgable" ),
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
     * @param VisualizationUpdateRequest $request
     * @return ApiSuccessResponse|ResourceNotFoundException
     * @throws Throwable
     */
    public function update(VisualizationUpdateRequest $request): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            $dataApp = $request->validated();
            return new ApiSuccessResponse(
                new VisualizationApplicationResource($this->appVisualization->update($dataApp)),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (CustomException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Delete(
     *       path="/api/v1/lk/applications/visualization/delete",
     *       operationId="LKAppVisualizationDelete",
     *       security={{"bearerAuth":{}}},
     *       tags={"Lk|Визуализация"},
     *       summary="Полностью удаление визуализации",
     *       @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="ID визуализации",
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
     * @param VisualizationShowRequest $request
     * @return ResourceNotFoundException|ApiSuccessResponse
     * @throws CustomException|Throwable
     */
    public function delete(VisualizationShowRequest $request): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            $appData = $request->validated();
            return new ApiSuccessResponse(
                $this->appVisualization->delete($appData['id']),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Patch(
     *       path="/api/v1/lk/applications/visualization/image/delete",
     *       operationId="LKAppVisualizationDeleteImage",
     *       security={{"bearerAuth":{}}},
     *       tags={"Lk|Визуализация"},
     *       summary="Удаление изображение визуализации",
     *       @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="ID визуализации",
     *         @OA\Schema(
     *               type="integer",
     *               example="1",
     *           )
     *       ),
     *       @OA\RequestBody(
     *           @OA\MediaType(
     *               mediaType="application/vnd.api+json",
     *               @OA\Schema(
     *                   @OA\Property(property="image", type="integer",example="http://newapiartrussiafair/api/v1/artist/nature.jpg"),
     *               ),
     *            ),
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
     * @param VisualizationImageDeleteRequest $request
     * @return ResourceNotFoundException|ApiSuccessResponse
     */
    public function deleteImage(VisualizationImageDeleteRequest $request): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            $appData = $request->validated();
            return new ApiSuccessResponse(
                $this->appVisualization->deleteImage($appData),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }
}
