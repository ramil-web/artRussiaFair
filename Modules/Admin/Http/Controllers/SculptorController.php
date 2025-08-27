<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\Participant\ListParticipantRequest;
use Admin\Http\Requests\Participant\StoreParticipantRequest;
use Admin\Http\Requests\Participant\UpdateParticipantRequest;
use Admin\Http\Resources\Participant\ParticipantResource;
use Admin\Http\Resources\Sculptor\SculptorCollection;
use Admin\Http\Resources\Sculptor\SculptorResource;
use Admin\Services\ParticipantService;
use App\Enums\ParticipantTypesEnum;
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

class SculptorController extends Controller
{
    private ParticipantTypesEnum $type;

    public function __construct(private ParticipantService $participantService)
    {
        $this->type = ParticipantTypesEnum::SCULPTOR();
    }


    /**
     * @OA\Post(
     *      path="/api/v1/admin/sculptor/store",
     *      operationId="AdminSculptorStore",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Скульпторы"},
     *      summary="добавление скульптора",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            required={"name", "description"},
     *            @OA\Property(property="event_id", type="array",
     *               example={1},
     *               @OA\Items(
     *               type="integer"
     *               ),
     *            ),
     *            @OA\Property(property="slug", description="Уникальное поле слаг", type="string", example="slug"),
     *            @OA\Property(property="sort_id", description="ID сортировки", type="integer", example="1"),
     *            @OA\Property(property="stand_id", description="ID стенда", type="string", example="1b"),
     *            @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *             ),
     *             @OA\Property(property="description", description="Описание скульптора", type="object",
     *                 @OA\Property(property="ru", example="Скульптор"),
     *                @OA\Property(property="en", example="sculptor")
     *             ),
     *            @OA\Property(property="image",description="Описание скульптора",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *            @OA\Property(property="images",description="Картинки художника",type="array",
     *                    example={
     *                      "http://newapiartrussiafair/api/v1/artist/nature.jpg",
     *                      "http://newapiartrussiafair/api/v1/artist/natureww.jpg"
     *                   },
     *                   @OA\Items(
     *                  type="string"
     *                  ),
     *                ),
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
     *             description="ID скульптора",
     *             type="object",
     *             @OA\Property(property="id", example="1", type="integer"),
     *             @OA\Property(property="type", example="sculptor", type="string"),
     *                @OA\Property(property="attributes", type="object",
     *                @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *             @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *             ),
     *             @OA\Property(property="description", description="Описание скульптора", type="object",
     *                 @OA\Property(property="ru", example="Скульптор"),
     *                @OA\Property(property="en", example="sculptor")
     *             ),
     *             @OA\Property(property="image",description="Изоброжение скульптора",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *             @OA\Property(property="stand_id",description="Номер стенда",type="string",example="2a"),
     *             @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *             @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *             @OA\Property(property="deleted_at", type="string", example=null),
     *             @OA\Property(property="slug", type="string", example="slug"),
     *             @OA\Property(property="images",description="Картинки художника",type="array",
     *                    example={
     *                      "http://newapiartrussiafair/api/v1/artist/nature.jpg",
     *                      "http://newapiartrussiafair/api/v1/artist/natureww.jpg"
     *                   },
     *                   @OA\Items(
     *                  type="string"
     *                  ),
     *                ),
     *             ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/sculptor/8"),
     *              ),
     *             @OA\Property(property="relationships", type="object",
     *                @OA\Property(property="events", type="object",
     *                  @OA\Property(property="data",type="object"),
     *                  @OA\Property(property="links",type="object",
     *                     @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/sculptor/6/relationships/eventgable"),
     *                     @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/sculptor/6/eventgable" ),
     *                 ),
     *                ),
     *              ),
     *            ),
     *                @OA\Property(property="metadata",type="object",
     *                   @OA\Property(property="message", example="Скульптор успешно добавлен"),
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
     * @param StoreParticipantRequest $request
     * @return ApiErrorResponse|ApiSuccessResponse
     */
    public function store(StoreParticipantRequest $request): ApiErrorResponse|ApiSuccessResponse
    {
        try {
            $dataApp = $request->validated();
            $dataApp['type'] = $this->type;
            return new ApiSuccessResponse(
                new  SculptorResource($this->participantService->create($dataApp)),
                ['message' => 'Скульптор успешно добавлен'],
                Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при добавление скульптора',
                $exception
            );
        }
    }

    /**
     * @OA\Get(
     *       path="/api/v1/admin/sculptor/{id}",
     *       operationId="AdminGetsculptor",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Скульпторы"},
     *       summary="Получить данные скульптора",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID скульптора",
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
     *              description="ID скульптора",
     *              type="object",
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="sculptor", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *             @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *             ),
     *             @OA\Property(property="description", description="Описание скульптора", type="object",
     *                 @OA\Property(property="ru", example="Скульптор"),
     *                @OA\Property(property="en", example="sculptor")
     *             ),
     *             @OA\Property(property="image",description="Изображение скульптора",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *             @OA\Property(property="stand_id",description="Номер стенда",type="string",example="2a"),
     *             @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *             @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *             @OA\Property(property="deleted_at", type="string", example=null),
     *             @OA\Property(property="slug", type="string", example="slug"),
     *             @OA\Property(property="images",description="Картинки художника",type="array",
     *                    example={
     *                      "http://newapiartrussiafair/api/v1/artist/nature.jpg",
     *                      "http://newapiartrussiafair/api/v1/artist/natureww.jpg"
     *                   },
     *                   @OA\Items(
     *                  type="string"
     *                  ),
     *                ),
     *             @OA\Property(property="eventgable", type="array",
     *                @OA\Items(
     *                   @OA\Property(property="event_id", type="integer", example="1"),
     *                   @OA\Property(property="eventgable_type",type="string",example="App\\Models\\sculptor"),
     *                   @OA\Property(property="eventgable_id", type="integer", example="7"),
     *                  ),
     *               ),
     *            ),
     *            @OA\Property(property="links", type="object",
     *               @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/sculptor/4"),
     *            ),
     *            @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/sculptor/6/relationships/eventgable"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/sculptor/6/eventgable" ),
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
            new  SculptorResource($this->participantService->show($id, $this->type)),
            ['message' => 'Ok'],
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *       path="/api/v1/admin/sculptor/all",
     *       operationId="Admin\AsculptorList",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Скульпторы"},
     *       summary="Получить список скульпторов",
     *       @OA\Parameter(
     *          name="filter[trashed]",
     *          in="query",
     *          description="Показать удаленных(архивных) (with/only)",
     *          @OA\Schema(
     *             type="string",
     *             enum={"with","only"},
     *          )
     *       ),
     *       @OA\Parameter(
     *          name="filter[id]",
     *          in="query",
     *          description="ID скульптора",
     *          @OA\Schema(
     *              type="integer",
     *          )
     *       ),
     *       @OA\Parameter(
     *          name="filter[name]",
     *          in="query",
     *          description="Имя скульптора",
     *          @OA\Schema(
     *             type="string",
     *          )
     *       ),
     *       @OA\Parameter(
     *           name="sort_by",
     *           in="query",
     *           description="Сортировка по поле",
     *           @OA\Schema(
     *              type="string",
     *              enum={"id","sort_id","name","description","stand_id","created_at", "updated_at"},
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
     *       @OA\Parameter(
     *             name="page",
     *             in="query",
     *             description="Номер страницы",
     *             @OA\Schema(
     *                type="integer",
     *                example=1
     *            )
     *        ),
     *        @OA\Parameter(
     *            name="per_page",
     *            in="query",
     *            description="Количество элементов на странице",
     *            @OA\Schema(
     *               type="integer",
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
     *              description="ID скульптора",
     *              type="array",
     *              @OA\Items(
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="sculptor", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *              @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                 @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                 @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *              ),
     *              @OA\Property(property="description", description="Описание скульптора", type="object",
     *                 @OA\Property(property="ru", example="Скульптор"),
     *                 @OA\Property(property="en", example="sculptor")
     *              ),
     *              @OA\Property(property="image",description="Изоброжение скульптора",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *              @OA\Property(property="stand_id",description="Номер стенда",type="string",example="2a"),
     *              @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *              @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *              @OA\Property(property="deleted_at", type="string", example=null),
     *              @OA\Property(property="slug", type="string", example="slug"),
     *              @OA\Property(property="images",description="Картинки художника",type="array",
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
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/sculptor/4"),
     *                ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/sculptor/6/relationships/eventgable"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/sculptor/6/eventgable" ),
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
     * @param ListParticipantRequest $request
     * @return SculptorCollection|ApiErrorResponse
     */
    public function list(ListParticipantRequest $request): SculptorCollection|ApiErrorResponse
    {
        $dataApp = $request->validated();
        $dataApp['type'] = $this->type;
        try {
            return new SculptorCollection($this->participantService->list($dataApp));
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при выводе списока скульпторов', $e);
        }
    }

    /**
     * @OA\Patch(
     *       path="/api/v1/admin/sculptor/update/{id}",
     *       operationId="AdminSculptorUpdate",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Скульпторы"},
     *       summary="Редактирование данные скульптора",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID скульптора",
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
     *            @OA\Property(property="type", description="Категория", type="string", example="artist"),
     *            @OA\Property(property="slug", type="string", example="artist_1"),
     *            @OA\Property(property="stand_id",description="Номер стенда",type="string",example="2a"),
     *            @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *             ),
     *             @OA\Property(property="description", description="Описание скульптора", type="object",
     *                 @OA\Property(property="ru", example="Скульптор"),
     *                @OA\Property(property="en", example="sculptor")
     *             ),
     *            @OA\Property(property="image",description="Изображение скульптора",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *            @OA\Property(property="images",description="Картинки художника",type="array",
     *                    example={
     *                      "http://newapiartrussiafair/api/v1/artist/nature.jpg",
     *                      "http://newapiartrussiafair/api/v1/artist/natureww.jpg"
     *                   },
     *                   @OA\Items(
     *                  type="string"
     *                  ),
     *                ),
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
     *              @OA\Property(property="type", example="sculptor", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *              @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                 @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                 @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *              ),
     *              @OA\Property(property="description", description="Описание скульптора", type="object",
     *                 @OA\Property(property="ru", example="Скульптор"),
     *                 @OA\Property(property="en", example="sculptor")
     *              ),
     *              @OA\Property(property="image",description="Изображение скульптора",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *              @OA\Property(property="stand_id",description="Номер стенда",type="string",example="2a"),
     *              @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *              @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *              @OA\Property(property="deleted_at", type="string", example=null),
     *              @OA\Property(property="slug", type="string", example="slug"),
     *               @OA\Property(property="images",description="Картинки художника",type="array",
     *                    example={
     *                      "http://newapiartrussiafair/api/v1/artist/nature.jpg",
     *                      "http://newapiartrussiafair/api/v1/artist/natureww.jpg"
     *                   },
     *                   @OA\Items(
     *                  type="string"
     *                  ),
     *                ),
     *              ),
     *             @OA\Property(property="links", type="object",
     *                @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/sculptor/4"),
     *             ),
     *             @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/sculptor/6/relationships/eventgable"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/sculptor/6/eventgable" ),
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
     * @param UpdateParticipantRequest $request
     * @return ApiSuccessResponse|ResourceNotFoundException
     */
    public function update(int $id, UpdateParticipantRequest $request): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            $dataApp = $request->validated();
            $model = $this->participantService->update($id, $dataApp, $this->type);
            return new ApiSuccessResponse(
                new ParticipantResource($model, $model->type),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Delete(
     *       path="/api/v1/admin/sculptor/delete/{id}",
     *       operationId="AdminSculptorDelete",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Скульпторы"},
     *       summary="Полностью удаление скульптора",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID скульптора",
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
                $this->participantService->delete($id, $this->type, ParticipantService::DELETE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Delete(
     *       path="/api/v1/admin/sculptor/archive/{id}",
     *       operationId="AdminSculptorArchive",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Скульпторы"},
     *       summary="Добавить скульптора в архив",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID скульптора",
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
                $this->participantService->delete($id, $this->type, ParticipantService::ARCHIVE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Patch (
     *    path="/api/v1/admin/sculptor/restore/{id}",
     *    operationId="Restoresculptor",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Скульпторы"},
     *    summary="Восстановить скульптора из архива",
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
     *               description="ID скульптора",
     *               type="object",
     *               @OA\Property(property="id", example="1", type="integer"),
     *               @OA\Property(property="type", example="sculptor", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *                  @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *               @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                  @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                  @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *               ),
     *               @OA\Property(property="description", description="Описание скульптора", type="object",
     *                  @OA\Property(property="ru", example="Скульптор"),
     *                  @OA\Property(property="en", example="sculptor")
     *               ),
     *               @OA\Property(property="image",description="Изображение скульптора",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *               @OA\Property(property="stand_id",description="Номер стенда",type="string",example="2a"),
     *               @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *               @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *               @OA\Property(property="deleted_at", type="string", example=null),
     *               @OA\Property(property="slug", type="string", example="slug"),
     *               @OA\Property(property="images",description="Картинки художника",type="array",
     *                    example={
     *                      "http://newapiartrussiafair/api/v1/artist/nature.jpg",
     *                      "http://newapiartrussiafair/api/v1/artist/natureww.jpg"
     *                   },
     *                   @OA\Items(
     *                  type="string"
     *                  ),
     *                ),
     *               @OA\Property(property="eventgable", type="array",
     *                  @OA\Items(
     *                     @OA\Property(property="event_id", type="integer", example="1"),
     *                     @OA\Property(property="eventgable_type",type="string",example="App\\Models\\sculptor"),
     *                     @OA\Property(property="eventgable_id", type="integer", example="7"),
     *                    ),
     *                 ),
     *              ),
     *              @OA\Property(property="links", type="object",
     *                  @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/sculptor/4"),
     *              ),
     *              @OA\Property(property="relationships", type="object",
     *                  @OA\Property(property="events", type="object",
     *                    @OA\Property(property="data",type="object"),
     *                    @OA\Property(property="links",type="object",
     *                       @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/sculptor/6/relationships/eventgable"),
     *                       @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/sculptor/6/eventgable" ),
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
     *                 @OA\Property(property="detail", example="Such an sculptor does not exist in the archive."),
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
        if ($this->participantService->checkData($id)) {
            $this->participantService->restore($id);
            return new ApiSuccessResponse(
                new SculptorResource($this->participantService->show($id, $this->type)),
                ['message' => 'успешно восстановлен'],
                ResponseAlias::HTTP_OK
            );
        }
        return new ApiErrorResponse(
            'Such an sculptor does not exist in the archive.',
            null,
            ResponseAlias::HTTP_NOT_FOUND
        );
    }
}
