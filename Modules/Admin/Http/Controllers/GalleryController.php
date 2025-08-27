<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\Participant\ListParticipantRequest;
use Admin\Http\Requests\Participant\StoreParticipantRequest;
use Admin\Http\Requests\Participant\UpdateParticipantRequest;
use Admin\Http\Resources\Gallery\GalleryCollection;
use Admin\Http\Resources\Gallery\GalleryResource;
use Admin\Http\Resources\Participant\ParticipantResource;
use Admin\Services\ParticipantService;
use App\Enums\ParticipantTypesEnum;
use App\Exceptions\CustomException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class GalleryController extends Controller
{
    private ParticipantTypesEnum $type;

    public function __construct(protected ParticipantService $participantService)
    {
        $this->type = ParticipantTypesEnum::GALLERY();
    }


    /**
     * @OA\Post(
     *      path="/api/v1/admin/gallery/store",
     *      operationId="AdmingalleryStore",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Галереи"},
     *      summary="Добавление галереи",
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
     *            @OA\Property(property="slug", description="Уникальное поле слаг", type="ыекштп", example="gallery_!"),
     *            @OA\Property(property="sort_id", description="ID сортировки", type="integer", example="1"),
     *            @OA\Property(property="stand_id", description="ID стенда", type="string", example="1b"),
     *              @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                  @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                  @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *               ),
     *               @OA\Property(property="description", description="Описание галереи", type="object",
     *                  @OA\Property(property="ru", example="Галерея"),
     *                  @OA\Property(property="en", example="gallery")
     *               ),
     *            @OA\Property(property="image",description="Описание галереи",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
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
     *             description="ID галереи",
     *             type="object",
     *             @OA\Property(property="id", example="1", type="integer"),
     *             @OA\Property(property="type", example="gallery", type="string"),
     *                @OA\Property(property="attributes", type="object",
     *                @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *             @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                  @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                  @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *              ),
     *              @OA\Property(property="description", description="Описание галереи", type="object",
     *                  @OA\Property(property="ru", example="Галерея"),
     *                  @OA\Property(property="en", example="gallery")
     *              ),
     *              @OA\Property(property="image",description="Изоброжение галереи",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *              @OA\Property(property="stand_id",description="Номер стенда",type="string",example="2a"),
     *              @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *              @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *              @OA\Property(property="deleted_at", type="string", example=null),
     *              @OA\Property(property="slug", type="string", example="gallery_1"),
     *              @OA\Property(property="images",description="Картинки художника",type="array",
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
     *                 @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/gallery/8"),
     *              ),
     *             @OA\Property(property="relationships", type="object",
     *                @OA\Property(property="events", type="object",
     *                  @OA\Property(property="data",type="object"),
     *                  @OA\Property(property="links",type="object",
     *                     @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/gallery/6/relationships/eventgable"),
     *                     @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/gallery/6/eventgable" ),
     *                 ),
     *                ),
     *              ),
     *            ),
     *                @OA\Property(property="metadata",type="object",
     *                   @OA\Property(property="message", example="Галерея успешно добавлен"),
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
                new  GalleryResource($this->participantService->create($dataApp)),
                ['message' => 'Художник успешно добавлен'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при добавление галереи',
                $exception
            );
        }
    }

    /**
     * @OA\Get(
     *       path="/api/v1/admin/gallery/{id}",
     *       operationId="AdminGetgallery",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Галереи"},
     *       summary="Получить данные галереи",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID галереи",
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
     *              description="ID галереи",
     *              type="object",
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="gallery", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *              @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                  @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                  @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *               ),
     *               @OA\Property(property="description", description="Описание галереи", type="object",
     *                  @OA\Property(property="ru", example="Галерея"),
     *                  @OA\Property(property="en", example="gallery")
     *               ),
     *               @OA\Property(property="image",description="Изображение галереи",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *               @OA\Property(property="stand_id",description="Номер стенда",type="string",example="2a"),
     *               @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *               @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *               @OA\Property(property="deleted_at", type="string", example=null),
     *               @OA\Property(property="slug", type="string", example="gallery_1"),
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
     *                        @OA\Property(property="eventgable_type", type="string", example="App\\Models\\gallery"),
     *                         @OA\Property(property="eventgable_id", type="integer", example="7"),
     *                      ),
     *                  ),
     *              ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/gallery/4"),
     *                ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/gallery/6/relationships/eventgable"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/gallery/6/eventgable" ),
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
            new  GalleryResource($this->participantService->show($id, $this->type)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/gallery/all",
     *    operationId="Admin\AgalleryList",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Галереи"},
     *    summary="Получить список всех галереи",
     *    @OA\Parameter(
     *        name="filter[category]",
     *        in="query",
     *        description="Фильтр по category событие, пока modern|classic",
     *        required=false,
     *        @OA\Schema(
     *            type="string",
     *        )
     *      ),
     *    @OA\Parameter(
     *       name="filter[trashed]",
     *       in="query",
     *       description="Показать удаленных(архивных) (with/only)",
     *       @OA\Schema(
     *           type="string",
     *           enum={"with","only"},
     *          )
     *       ),
     *      @OA\Parameter(
     *          name="filter[id]",
     *          in="query",
     *          description="ID галереи",
     *          @OA\Schema(
     *             type="integer",
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="filter[name]",
     *         in="query",
     *         description="Название галереи",
     *         @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *      @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Сортировка по поле",
     *         @OA\Schema(
     *            type="string",
     *            enum={"id","sort_id","name","description","stand_id","created_at", "updated_at"},
     *           )
     *       ),
     *       @OA\Parameter(
     *          name="order_by",
     *          in="query",
     *          description="Порядок сортировки",
     *          @OA\Schema(
     *             type="string",
     *             enum={"ASC", "DESC"},
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
     *          name="per_page",
     *          in="query",
     *          description="Пагинация количество элементов на странице",
     *          @OA\Schema(
     *             type="integer",
     *         )
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
     *              description="ID галереи",
     *              type="array",
     *              @OA\Items(
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="gallery", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *              @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                  @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                  @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *               ),
     *               @OA\Property(property="description", description="Описание галереи", type="object",
     *                  @OA\Property(property="ru", example="Галерея"),
     *                  @OA\Property(property="en", example="gallery")
     *               ),
     *               @OA\Property(property="image",description="Изоброжение галереи",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *               @OA\Property(property="stand_id",description="Номер стенда",type="string",example="2a"),
     *               @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *               @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *               @OA\Property(property="deleted_at", type="string", example=null),
     *               @OA\Property(property="slug", type="string", example="gallery_1"),
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
     *              @OA\Property(property="links", type="object",
     *                 @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/gallery/4"),
     *              ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/gallery/6/relationships/eventgable"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/gallery/6/eventgable" ),
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
     * @return GalleryCollection|ApiErrorResponse
     */
    public function list(ListParticipantRequest $request): GalleryCollection|ApiErrorResponse
    {
        $dataApp = $request->validated();
        $dataApp['type'] = $this->type;
        try {
            return new GalleryCollection($this->participantService->list($dataApp));
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при выводе списока галереи', $e);
        }
    }

    /**
     * @OA\Patch(
     *       path="/api/v1/admin/gallery/update/{id}",
     *       operationId="AdmingalleryUpdate",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Галереи"},
     *       summary="Редактирование данные галереи",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID галереи",
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
     *            @OA\Property(property="slug", type="string", example="artist_1"),
     *            @OA\Property(property="type", description="Категория", type="string", example="artist"),
     *            @OA\Property(property="stand_id",description="Номер стенда",type="string",example="2a"),
     *            @OA\Property(property="name", description="Название галереи", type="object",
     *                  @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                  @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *               ),
     *               @OA\Property(property="description", description="Описание галереи", type="object",
     *                  @OA\Property(property="ru", example="Галерея"),
     *                  @OA\Property(property="en", example="gallery")
     *               ),
     *            @OA\Property(property="image",description="Изображение галереи",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
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
     *              @OA\Property(property="type", example="gallery", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *              @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                  @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                  @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *               ),
     *               @OA\Property(property="description", description="Описание галереи", type="object",
     *                  @OA\Property(property="ru", example="Галерея"),
     *                  @OA\Property(property="en", example="gallery")
     *               ),
     *               @OA\Property(property="image",description="Изображение галереи",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *               @OA\Property(property="stand_id",description="Номер стенда",type="string",example="2a"),
     *               @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *               @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *               @OA\Property(property="deleted_at", type="string", example=null),
     *               @OA\Property(property="slug", type="string", example="gallery_1"),
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
     *              @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/gallery/4"),
     *                ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/gallery/6/relationships/eventgable"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/gallery/6/eventgable" ),
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
     * @throws CustomException
     * @throws Throwable
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
     *       path="/api/v1/admin/gallery/delete/{id}",
     *       operationId="AdmingalleryDelete",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Галереи"},
     *       summary="Полностью удаление галереи",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID галереи",
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
     *       path="/api/v1/admin/gallery/archive/{id}",
     *       operationId="AdmingalleryArchive",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Галереи"},
     *       summary="Добавить галереи в архив",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID галереи",
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
     * @OA\Patch(
     *    path="/api/v1/admin/gallery/restore/{id}",
     *    operationId="Restoregallery",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Галереи"},
     *    summary="Восстановить галереи из архива",
     *    @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       description="ID галереи",
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
     *               description="ID галереи",
     *               type="object",
     *               @OA\Property(property="id", example="1", type="integer"),
     *               @OA\Property(property="type", example="gallery", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *                  @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *              @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                  @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                  @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *               ),
     *               @OA\Property(property="description", description="Описание галереи", type="object",
     *                  @OA\Property(property="ru", example="Галерея"),
     *                  @OA\Property(property="en", example="gallery")
     *               ),
     *               @OA\Property(property="image",description="Изображение галереи",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *               @OA\Property(property="stand_id",description="Номер стенда",type="string",example="2a"),
     *               @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *               @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *               @OA\Property(property="deleted_at", type="string", example=null),
     *               @OA\Property(property="slug", type="string", example="gallery_1"),
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
     *                        @OA\Property(property="eventgable_type",type="string",example="App\\Models\\gallery"),
     *                        @OA\Property(property="eventgable_id", type="integer", example="7"),
     *                     ),
     *                  ),
     *              ),
     *              @OA\Property(property="links", type="object",
     *                 @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/gallery/4"),
     *              ),
     *             @OA\Property(property="relationships", type="object",
     *                  @OA\Property(property="events", type="object",
     *                    @OA\Property(property="data",type="object"),
     *                    @OA\Property(property="links",type="object",
     *                       @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/gallery/6/relationships/eventgable"),
     *                       @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/gallery/6/eventgable" ),
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
     *                 @OA\Property(property="detail", example="Such an gallery does not exist in the archive."),
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
                new GalleryResource($this->participantService->show($id, $this->type)),
                ['message' => 'успешно восстановлен'],
                ResponseAlias::HTTP_OK
            );
        }
        return new ApiErrorResponse(
            'Such an gallery does not exist in the archive.',
            null,
            ResponseAlias::HTTP_NOT_FOUND
        );
    }
}
