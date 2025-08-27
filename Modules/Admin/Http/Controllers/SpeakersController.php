<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\Speaker\ListSpeakerRequest;
use Admin\Http\Requests\Speaker\ShowSpeakerRequest;
use Admin\Http\Requests\Speaker\StoreSpeakerRequest;
use Admin\Http\Requests\Speaker\UpdateSpeakerRequest;
use Admin\Http\Resources\Speaker\SpeakerCollection;
use Admin\Http\Resources\Speaker\SpeakerResource;
use Admin\Services\SpeakerService;
use App\Enums\SpeakerTypesEnum;
use App\Exceptions\CustomException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class SpeakersController extends Controller
{

    protected SpeakerTypesEnum $type;

    public function __construct(public SpeakerService $speakerService)
    {
        $this->type = SpeakerTypesEnum::SPEAKER();
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/speaker/store",
     *      operationId="AdminSpeakerStore",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Спикеры"},
     *      summary="Создание спикера",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            required={"name", "description"},
     *            @OA\Property(property="event_id", type="array",
     *               example={1, 2},
     *               @OA\Items(
     *               type="integer"
     *               ),
     *            ),
     *            @OA\Property(property="sort_id", description="ID сортировки", type="integer", example="1"),
     *            @OA\Property(property="name", description="ФИО хчудожника", type="object",
     *               @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *               @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *            ),
     *            @OA\Property(property="description", description="Описание художника", type="object",
     *               @OA\Property(property="ru", example="Художник"),
     *               @OA\Property(property="en", example="Artist")
     *            ),
     *            @OA\Property(property="full_description", description="Полное описание", type="object",
     *                @OA\Property(property="ru", example="Художник"),
     *                @OA\Property(property="en", example="Artist")
     *             ),
     *            @OA\Property(property="position", description="Должность художника", type="object",
     *                @OA\Property(property="ru", example="Директор"),
     *                @OA\Property(property="en", example="Director")
     *             ),
     *            @OA\Property(property="image",description="Описание спикера",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
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
     *             description="ID Спикера",
     *             type="object",
     *             @OA\Property(property="id", example="1", type="integer"),
     *             @OA\Property(property="type", example="speaker", type="string"),
     *                @OA\Property(property="attributes", type="object",
     *                @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                     @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                     @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *                 ),
     *                 @OA\Property(property="description", description="Описание художника", type="object",
     *                     @OA\Property(property="ru", example="Художник"),
     *                     @OA\Property(property="en", example="Artist")
     *                 ),
     *               @OA\Property(property="full_description", description="Полное писание", type="object",
     *                      @OA\Property(property="ru", example="Художник"),
     *                      @OA\Property(property="en", example="Artist")
     *                  ),
     *                @OA\Property(property="image",description="Описание спикера",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *                @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *             ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/speaker/8"),
     *              ),
     *             @OA\Property(property="relationships", type="object",
     *                @OA\Property(property="events", type="object",
     *                  @OA\Property(property="data",type="object"),
     *                  @OA\Property(property="links",type="object",
     *                     @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/speaker/6/relationships/eventgable"),
     *                     @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/speaker/6/eventgable" ),
     *                 ),
     *                ),
     *              ),
     *            ),
     *                @OA\Property(property="metadata",type="object",
     *                   @OA\Property(property="message", example="Спикер успешно создан"),
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
     * @param StoreSpeakerRequest $request
     * @return ApiErrorResponse|ApiSuccessResponse
     */
    public function store(StoreSpeakerRequest $request): ApiErrorResponse|ApiSuccessResponse
    {
        try {
            $dataApp = $request->validated();
            $dataApp['type'] = $this->type;
            return new ApiSuccessResponse(
                new  SpeakerResource($this->speakerService->create($dataApp)),
                ['message' => 'Спикер успешно создан'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при создании спикера',
                $exception
            );
        }
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/speaker/{id}",
     *    operationId="AdminGetSpeaker",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Спикеры"},
     *    summary="Получить данные спикера",
     *    @OA\Parameter(
     *       name="filter[category]",
     *       in="query",
     *       description="Фильтр по category пока modern|classic|etc",
     *       required=false,
     *       @OA\Schema(
     *          type="string",
     *          )
     *       ),
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID спикера",
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
     *              description="ID спикера",
     *              type="object",
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="speaker", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *              @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                 @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                 @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *              ),
     *              @OA\Property(property="description", description="Описание художника", type="object",
     *                 @OA\Property(property="ru", example="Художник"),
     *                 @OA\Property(property="en", example="Artist")
     *              ),
     *            @OA\Property(property="full_description", description="Полное писание", type="object",
     *                       @OA\Property(property="ru", example="Художник"),
     *                       @OA\Property(property="en", example="Artist")
     *                   ),
     *              @OA\Property(property="image",description="Изоброжение спикера",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *              @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *              @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="eventgable", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="event_id", type="integer", example="1"),
     *                          @OA\Property(property="eventgable_type", type="string", example="App\\Models\\Speaker"),
     *                          @OA\Property(property="eventgable_id", type="integer", example="7"),
     *                      ),
     *                  ),
     *              ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/speaker/4"),
     *                ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/speaker/6/relationships/eventgable"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/speaker/6/eventgable" ),
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
     * @param ShowSpeakerRequest $request
     * @return ApiSuccessResponse
     */
    public function show(int $id, ShowSpeakerRequest $request): ApiSuccessResponse
    {
        $data = $request->validated();
        return new ApiSuccessResponse(
            new  SpeakerResource($this->speakerService->show($id, $this->type, $data)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/speaker/all",
     *    operationId="AdminSpeakerList",
     *    tags={"Admin|Спикеры"},
     *    summary="Получить список всех спикеров",
     *    @OA\Parameter(
     *       name="filter[category]",
     *       in="query",
     *       description="Фильтр по category пока modern|classic|etc",
     *       required=false,
     *       @OA\Schema(
     *          type="string",
     *          )
     *       ),
     *       @OA\Parameter(
     *          name="filter[trashed]",
     *          in="query",
     *          description="Показать удаленных(архивных) (with/only)",
     *          @OA\Schema(
     *             type="string",
     *             enum={"with","only"},
     *           )
     *       ),
     *       @OA\Parameter(
     *          name="filter[id]",
     *          in="query",
     *          description="ID спикера",
     *          @OA\Schema(
     *                type="integer",
     *              )
     *        ),
     *      @OA\Parameter(
     *          name="filter[name]",
     *          in="query",
     *          description="Имя спикера",
     *          @OA\Schema(
     *             type="string",
     *          )
     *       ),
     *       @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          description="Сортировка по поле",
     *          @OA\Schema(
     *             type="string",
     *             enum={"id","sort_id","name","description","created_at", "updated_at"},
     *          )
     *        ),
     *        @OA\Parameter(
     *           name="order_by",
     *           in="query",
     *           description="Порядок сортировки",
     *           @OA\Schema(
     *              type="string",
     *              enum={"ASC", "DESC"},
     *           )
     *        ),
     *       @OA\Parameter(
     *            name="page",
     *            in="query",
     *            description="Номер страницы",
     *            @OA\Schema(
     *               type="integer",
     *               example=1
     *           )
     *         ),
     *       @OA\Parameter(
     *           name="per_page",
     *           in="query",
     *           description="Количество элементов на странице",
     *           @OA\Schema(
     *              type="integer",
     *              example=10
     *          )
     *        ),
     *      @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            @OA\Property(
     *              property="data",
     *              description="ID спикера",
     *              type="array",
     *              @OA\Items(
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="speaker", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *              @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *             ),
     *             @OA\Property(property="description", description="Описание художника", type="object",
     *                @OA\Property(property="ru", example="Художник"),
     *                @OA\Property(property="en", example="Artist")
     *             ),
     *            @OA\Property(property="position", description="Должность художника", type="object",
     *                 @OA\Property(property="ru", example="Директор"),
     *                 @OA\Property(property="en", example="Director")
     *              ),
     *             @OA\Property(property="full_description", description="Полное писание", type="object",
     *                       @OA\Property(property="ru", example="Художник"),
     *                       @OA\Property(property="en", example="Artist")
     *                   ),
     *                 @OA\Property(property="image",description="Изоброжение спикера",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *              ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/speaker/4"),
     *                ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/speaker/6/relationships/eventgable"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/speaker/6/eventgable" ),
     *                  ),
     *                 ),
     *               ),
     *             ),
     *           ),
     *           @OA\Property(property="links", type="object"),
     *           @OA\Property(property="meta",type="object",),
     *           ),
     *        ),
     *      ),
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
     * @param ListSpeakerRequest $request
     * @return SpeakerCollection|ApiErrorResponse
     */
    public function list(ListSpeakerRequest $request): SpeakerCollection|ApiErrorResponse
    {
        $dataApp = $request->validated();
        $dataApp['type'] = $this->type;
        try {
            return new SpeakerCollection($this->speakerService->list($dataApp));
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при выводе списока спикеров', $e);
        }
    }

    /**
     * @OA\Patch(
     *       path="/api/v1/admin/speaker/update/{id}",
     *       operationId="AdminSpeakerUpdate",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Спикеры"},
     *       summary="Редактирование данные спикера",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID спикера",
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
     *                example={1, 2},
     *                @OA\Items(
     *                type="integer"
     *                ),
     *            ),
     *            @OA\Property(property="sort_id", description="ID сортировки", type="integer", example="1"),
     *            @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *             ),
     *             @OA\Property(property="description", description="Описание художника", type="object",
     *                @OA\Property(property="ru", example="Художник"),
     *                @OA\Property(property="en", example="Artist")
     *             ),
     *            @OA\Property(property="position", description="Должность художника", type="object",
     *                 @OA\Property(property="ru", example="Директор"),
     *                 @OA\Property(property="en", example="Director")
     *              ),
     *            @OA\Property(property="full_description", description="Полное описание", type="object",
     *                 @OA\Property(property="ru", example="Художник"),
     *                 @OA\Property(property="en", example="Artist")
     *              ),
     *            @OA\Property(property="image",description="Изображение спикера",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
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
     *              @OA\Property(property="type", example="speaker", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *              @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                 @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                 @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *              ),
     *              @OA\Property(property="description", description="Описание художника", type="object",
     *                 @OA\Property(property="ru", example="Художник"),
     *                 @OA\Property(property="en", example="Artist")
     *              ),
     *              @OA\Property(property="full_description", description="Полное описание", type="object",
     *                 @OA\Property(property="ru", example="Художник"),
     *                 @OA\Property(property="en", example="Artist")
     *              ),
     *                 @OA\Property(property="image",description="Изображение спикера",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *              ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/speaker/4"),
     *                ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/speaker/6/relationships/eventgable"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/speaker/6/eventgable" ),
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
     * @param UpdateSpeakerRequest $request
     * @return ApiSuccessResponse|ResourceNotFoundException
     * @throws Throwable
     * @throws CustomException
     */
    public function update(int $id, UpdateSpeakerRequest $request): ApiSuccessResponse|ResourceNotFoundException
    {
        $dataApp = $request->validated();
        try {
            return new ApiSuccessResponse(
                new SpeakerResource($this->speakerService->update($id, $dataApp, $this->type)),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Delete(
     *       path="/api/v1/admin/speaker/delete/{id}",
     *       operationId="AdminSpeakerDelete",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Спикеры"},
     *       summary="Польность удаление спикера",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID спикера",
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
    public function delete(int $id): ResourceNotFoundException|ApiSuccessResponse
    {
        try {
            return new ApiSuccessResponse(
                $this->speakerService->delete($id, $this->type, SpeakerService::DELETE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Delete(
     *       path="/api/v1/admin/speaker/archive/{id}",
     *       operationId="AdminSpeakerArchive",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Спикеры"},
     *       summary="Добавить cпикера в архив",
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
     * @throws CustomException
     */
    public function archive(int $id): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            return new ApiSuccessResponse(
                $this->speakerService->delete($id, $this->type, SpeakerService::ARCHIVE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Patch (
     *    path="/api/v1/admin/speaker/restore/{id}",
     *    operationId="RestoreSpeaker",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Спикеры"},
     *    summary="Восстановить cпикера из архива",
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
     *              @OA\Property(property="full_description", description="Полное описание", type="object",
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
        if ($this->speakerService->checkData($id)) {
            $this->speakerService->restore($id);
            return new ApiSuccessResponse(
                new SpeakerResource($this->speakerService->show($id, $this->type)),
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
