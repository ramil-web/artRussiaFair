<?php

namespace Lk\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Lk\Http\Requests\InformationForPlacement\InformationForPlacementImageDeleteRequest;
use Lk\Http\Requests\InformationForPlacement\InformationForPlacementListRequest;
use Lk\Http\Requests\InformationForPlacement\InformationForPlacementShowRequest;
use Lk\Http\Requests\InformationForPlacement\InformationForPlacementStoreRequest;
use Lk\Http\Requests\InformationForPlacement\InformationForPlacementUpdateRequest;
use Lk\Http\Resources\InformationForPlacement\InformationForPlacementCollection;
use Lk\Http\Resources\InformationForPlacement\InformationForPlacementResource;
use Lk\Services\InformationForPlacementService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class InformationForPlacementController extends Controller
{
    public function __construct(protected InformationForPlacementService $placementService)
    {
    }

    /**
     * @OA\Post(
     *      path="/api/v1/lk/applications/information-placement/store",
     *      tags={"Lk|Информация для размещения"},
     *      security={{"bearerAuth":{}}},
     *      summary="Создает информацию для размещения",
     *      operationId="lkCreateInformasionForPlacement",
     *      @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=true,
     *         description="Для кого это, для сайта или каталога",
     *         @OA\Schema(
     *            type="string",
     *            enum={"for_app","for_catalog","for_social_network", "for_general_information"},
     *          )
     *       ),
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/vnd.api+json",
     *             @OA\Schema(
     *                @OA\Property(property="name", description="Имя/псевдоним", type="object",
     *                @OA\Property(property="ru", example="Что-то"),
     *             ),
     *             @OA\Property(property="description", description="Описание", type="object",
     *                @OA\Property(property="ru", example="Что-то, с чем-то"),
     *             ),
     *             @OA\Property(property="user_application_id", type="integer",example=1),
     *             @OA\Property(property="photo", type="string",example="http://newapiartrussiafair/api/v1/artist/nature.jpg"),
     *             @OA\Property(property="social_network", type="object",
     *                    @OA\Property(property="title", type="string"),
     *                    @OA\Property(property="url", type="string"),
     *                    example={{"title":"VK","url":"https://vk.com"},{"title":"telegram", "url":"https://telegram"}},
     *             ),
     *             @OA\Property(property="url",description="Картинки информацию для размещения",type="array",
     *                     example={
     *                       "http://newapiartrussiafair/api/v1/artist/nature.jpg",
     *                       "http://newapiartrussiafair/api/v1/artist/natureww.jpg"
     *                    },
     *                 @OA\Items(type="string"),
     *              ),
     *             ),
     *          ),
     *     ),
     *      @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            @OA\Property(
     *              property="data",
     *              description="ID художника",
     *              type="object",
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="information-placement", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *             @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *             @OA\Property(property="user_application_id",description="Идентификатор заявки",type="integer", example="2"),
     *             @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *             ),
     *             @OA\Property(property="description", description="Описание художника", type="object",
     *                @OA\Property(property="ru", example="Художник"),
     *                @OA\Property(property="en", example="Artist")
     *            ),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="deleted_at", type="string", example=null),
     *                 @OA\Property(property="photo", type="string", example="http://newapiartrussiafair/api/v1/artist/nature.jpg"),
     *                 @OA\Property(property="url",description="Картинки художника",type="array",
     *                    example={
     *                      "http://newapiartrussiafair/api/v1/artist/nature.jpg",
     *                      "http://newapiartrussiafair/api/v1/artist/natureww.jpg"
     *                   },
     *                   @OA\Items(
     *                  type="string"
     *                  ),
     *                ),
     *              ),
     *              @OA\Property(property="links", type="object",
     *                  @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/applications/information-placement/show?id=4"),
     *               ),
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
     *                    @OA\Property(property="message", example="Художник успешно добавлен"),
     *                  ),
     *               ),
     *              ),
     *            ),
     *       @OA\Response(response=401, description="Unauthenticated"),
     *       @OA\Response(response=400,description="Bad Request"),
     *       @OA\Response(response=404,description="not found"),
     *             @OA\Response(response=403,description="Forbidden",
     *            @OA\JsonContent(
     *                @OA\Property(property="errors", type="array",
     *                @OA\Items(
     *                    @OA\Property(property="status", example="403"),
     *                    @OA\Property(property="detail", example="User does not have the right roles.")
     *                ),
     *             ),
     *           ),
     *        ),
     *       @OA\Response(response=500,description="Server error")
     *  )
     */
    public function store(InformationForPlacementStoreRequest $request): ApiSuccessResponse
    {
        $appData = $request->validated();
        return new ApiSuccessResponse(
            new InformationForPlacementResource($this->placementService->create($appData)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *      path="/api/v1/lk/applications/information-placement/show",
     *      tags={"Lk|Информация для размещения"},
     *      security={{"bearerAuth":{}}},
     *      summary="Получит информацию для размещения",
     *      operationId="lkGetInformationForPlacement",
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          required=true,
     *          description="ID информации для размещение",
     *
     *        @OA\Schema(
     *             type="integer"
     *        )
     *     ),
     *      @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *               property="data",
     *               description="ID художника",
     *               type="object",
     *               @OA\Property(property="id", example="1", type="integer"),
     *               @OA\Property(property="type", example="information-placement", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *              @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *              @OA\Property(property="user_application_id",description="Идентификатор заявки",type="integer", example="2"),
     *              @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                 @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                 @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *              ),
     *              @OA\Property(property="description", description="Описание художника", type="object",
     *                 @OA\Property(property="ru", example="Художник"),
     *                 @OA\Property(property="en", example="Artist")
     *             ),
     *                  @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                  @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                  @OA\Property(property="deleted_at", type="string", example=null),
     *                  @OA\Property(property="photo", type="string", example="http://newapiartrussiafair/api/v1/artist/nature.jpg"),
     *                  @OA\Property(property="url",description="Картинки художника",type="array",
     *                     example={
     *                       "http://newapiartrussiafair/api/v1/artist/nature.jpg",
     *                       "http://newapiartrussiafair/api/v1/artist/natureww.jpg"
     *                    },
     *                    @OA\Items(
     *                   type="string"
     *                   ),
     *                 ),
     *               ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/applications/information-placement/show?id=4"),
     *                ),
     *               @OA\Property(property="relationships", type="object",
     *                  @OA\Property(property="events", type="object",
     *                    @OA\Property(property="data",type="object"),
     *                    @OA\Property(property="links",type="object",
     *                       @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/artist/6/relationships/eventgable"),
     *                       @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/artist/6/eventgable" ),
     *                   ),
     *                  ),
     *                ),
     *              ),
     *                  @OA\Property(property="metadata",type="object",
     *                     @OA\Property(property="message", example="Художник успешно добавлен"),
     *                   ),
     *                ),
     *               ),
     *             ),
     *        @OA\Response(response=401, description="Unauthenticated"),
     *        @OA\Response(response=400,description="Bad Request"),
     *        @OA\Response(response=404,description="not found"),
     *              @OA\Response(response=403,description="Forbidden",
     *             @OA\JsonContent(
     *                 @OA\Property(property="errors", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="status", example="403"),
     *                     @OA\Property(property="detail", example="User does not have the right roles.")
     *                 ),
     *              ),
     *            ),
     *         ),
     *        @OA\Response(response=500,description="Server error")
     *   )
     * @param InformationForPlacementShowRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function show(InformationForPlacementShowRequest $request): ApiSuccessResponse
    {
        $appData = $request->validated();
        return new ApiSuccessResponse(
            new InformationForPlacementResource($this->placementService->show($appData['id'])),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *      path="/api/v1/lk/applications/information-placement/list",
     *      tags={"Lk|Информация для размещения"},
     *      security={{"bearerAuth":{}}},
     *      summary="Получит список информацию для размещения",
     *      operationId="lkGetInformationForPlacements",
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
     * @param InformationForPlacementListRequest $request
     * @return ApiSuccessResponse
     */
    public function list(InformationForPlacementListRequest $request): ApiSuccessResponse
    {
        $dataApp = $request->validated();
        return new ApiSuccessResponse(
            new InformationForPlacementCollection($this->placementService->list($dataApp)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }


    /**
     * @OA\Patch(
     *       path="/api/v1/lk/applications/information-placement/update",
     *       operationId="LKAppInformationForPlacementUpdate",
     *       security={{"bearerAuth":{}}},
     *       tags={"Lk|Информация для размещения"},
     *       summary="Редактирование информацию для размещения",
     *       @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="ID информации для размещения",
     *         @OA\Schema(
     *               type="integer",
     *               example="1",
     *           )
     *       ),
     *      @OA\Parameter(
     *          name="type",
     *          in="query",
     *          required=true,
     *          description="Для кого это, для сайта или каталога",
     *          @OA\Schema(
     *             type="string",
     *             enum={"for_app","for_catalog","for_social_network", "for_general_information"},
     *           )
     *        ),
     *       @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *              @OA\Schema(
     *                  @OA\Property(property="user_application_id", type="integer",example=1),
     *                  @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                  @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                  @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *               ),
     *               @OA\Property(property="description", description="Описание художника", type="object",
     *                  @OA\Property(property="ru", example="Художник"),
     *                  @OA\Property(property="en", example="Artist")
     *              ),
     *              @OA\Property(property="photo",description="Фрото художника",type="string", example="http://newapiartrussiafair/api/v1/artist/nature.jpg"),
     *              @OA\Property(property="social_network", type="object",
     *                 @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="url", type="string"),
     *                     example={{"title":"VK","url":"https://vk.com"},{"title":"telegram", "url":"https://telegram"}},
     *              ),
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
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *               property="data",
     *               description="ID художника",
     *               type="object",
     *               @OA\Property(property="id", example="1", type="integer"),
     *               @OA\Property(property="type", example="information-placement", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *              @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *              @OA\Property(property="user_application_id",description="Идентификатор заявки",type="integer", example="2"),
     *              @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                 @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                 @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *              ),
     *              @OA\Property(property="description", description="Описание художника", type="object",
     *                 @OA\Property(property="ru", example="Художник"),
     *                 @OA\Property(property="en", example="Artist")
     *             ),
     *                  @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                  @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                  @OA\Property(property="deleted_at", type="string", example=null),
     *                  @OA\Property(property="url",description="Картинки художника",type="array",
     *                     example={
     *                       "http://newapiartrussiafair/api/v1/artist/nature.jpg",
     *                       "http://newapiartrussiafair/api/v1/artist/natureww.jpg"
     *                    },
     *                    @OA\Items(
     *                   type="string"
     *                   ),
     *                 ),
     *               ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/applications/information-placement/show?id=4"),
     *                ),
     *               @OA\Property(property="relationships", type="object",
     *                  @OA\Property(property="events", type="object",
     *                    @OA\Property(property="data",type="object"),
     *                    @OA\Property(property="links",type="object",
     *                       @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/artist/6/relationships/eventgable"),
     *                       @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/artist/6/eventgable" ),
     *                   ),
     *                  ),
     *                ),
     *              ),
     *                  @OA\Property(property="metadata",type="object",
     *                     @OA\Property(property="message", example="Художник успешно добавлен"),
     *                   ),
     *                ),
     *               ),
     *             ),
     *        @OA\Response(response=401, description="Unauthenticated"),
     *        @OA\Response(response=400,description="Bad Request"),
     *        @OA\Response(response=404,description="not found"),
     *              @OA\Response(response=403,description="Forbidden",
     *             @OA\JsonContent(
     *                 @OA\Property(property="errors", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="status", example="403"),
     *                     @OA\Property(property="detail", example="User does not have the right roles.")
     *                 ),
     *              ),
     *            ),
     *         ),
     *        @OA\Response(response=500,description="Server error")
     *   )
     * @param InformationForPlacementUpdateRequest $request
     * @return ApiSuccessResponse|ResourceNotFoundException
     */
    public function update(InformationForPlacementUpdateRequest $request): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            $dataApp = $request->validated();
            return new ApiSuccessResponse(
                new InformationForPlacementResource($this->placementService->update($dataApp)),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (CustomException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Delete(
     *       path="/api/v1/lk/applications/information-placement/delete",
     *       operationId="LKAppInformationForPlacementDelete",
     *       security={{"bearerAuth":{}}},
     *       tags={"Lk|Информация для размещения"},
     *       summary="Полностью удаление информацию для размещения",
     *       @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="ID информации для размещения",
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
     * @param InformationForPlacementShowRequest $request
     * @return ResourceNotFoundException|ApiSuccessResponse
     * @throws CustomException
     */
    public function delete(InformationForPlacementShowRequest $request): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            $appData = $request->validated();
            return new ApiSuccessResponse(
                $this->placementService->delete($appData['id']),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Patch(
     *       path="/api/v1/lk/applications/information-placement/image/delete",
     *       operationId="LKAppInformationForPlacementDeleteImage",
     *       security={{"bearerAuth":{}}},
     *       tags={"Lk|Информация для размещения"},
     *       summary="Удаление изображение информации для размещения",
     *       @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="ID информации для размещения",
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
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *               property="data",
     *               description="ID художника",
     *               type="object",
     *               @OA\Property(property="id", example="1", type="integer"),
     *               @OA\Property(property="type", example="information-placement", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *              @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *              @OA\Property(property="user_application_id",description="Идентификатор заявки",type="integer", example="2"),
     *              @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                 @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                 @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *              ),
     *              @OA\Property(property="description", description="Описание художника", type="object",
     *                 @OA\Property(property="ru", example="Художник"),
     *                 @OA\Property(property="en", example="Artist")
     *             ),
     *                  @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                  @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                  @OA\Property(property="deleted_at", type="string", example=null),
     *                  @OA\Property(property="photo",description="Фото художника",type="tring", example="http://newapiartrussiafair/api/v1/artist/nature.jpg"),
     *                  @OA\Property(property="url",description="Картинки художника",type="array",
     *                     example={
     *                       "http://newapiartrussiafair/api/v1/artist/nature.jpg",
     *                       "http://newapiartrussiafair/api/v1/artist/natureww.jpg"
     *                    },
     *                    @OA\Items(
     *                   type="string"
     *                   ),
     *                 ),
     *               ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/applications/information-placement/show?id=4"),
     *                ),
     *               @OA\Property(property="relationships", type="object",
     *                  @OA\Property(property="events", type="object",
     *                    @OA\Property(property="data",type="object"),
     *                    @OA\Property(property="links",type="object",
     *                       @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/artist/6/relationships/eventgable"),
     *                       @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/artist/6/eventgable" ),
     *                   ),
     *                  ),
     *                ),
     *              ),
     *                  @OA\Property(property="metadata",type="object",
     *                     @OA\Property(property="message", example="Художник успешно добавлен"),
     *                   ),
     *                ),
     *               ),
     *             ),
     *        @OA\Response(response=401, description="Unauthenticated"),
     *        @OA\Response(response=400,description="Bad Request"),
     *        @OA\Response(response=404,description="not found"),
     *              @OA\Response(response=403,description="Forbidden",
     *             @OA\JsonContent(
     *                 @OA\Property(property="errors", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="status", example="403"),
     *                     @OA\Property(property="detail", example="User does not have the right roles.")
     *                 ),
     *              ),
     *            ),
     *         ),
     *        @OA\Response(response=500,description="Server error")
     *   )
     * @param InformationForPlacementImageDeleteRequest $request
     * @return ResourceNotFoundException|ApiSuccessResponse
     */
    public function deleteImage(
        InformationForPlacementImageDeleteRequest $request
    ): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            $appData = $request->validated();
            return new ApiSuccessResponse(
                $this->placementService->deleteImage($appData),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException|CustomException $e) {
            return new ResourceNotFoundException($e);
        }
    }
}
