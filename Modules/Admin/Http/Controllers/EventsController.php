<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\Event\ListEventRequest;
use Admin\Http\Requests\Event\ShowEventRequest;
use Admin\Http\Requests\Event\StoreEventRequest;
use Admin\Http\Requests\Event\UpdateEventRequest;
use Admin\Http\Resources\Artist\ArtistResource;
use Admin\Http\Resources\Event\EventCollection;
use Admin\Http\Resources\Event\EventResource;
use Admin\Services\EventService;
use App\Exceptions\CustomException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class EventsController extends Controller
{
    public function __construct(public EventService $eventService)
    {
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/events",
     *    operationId="EventAll",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|События"},
     *    summary="Получить список ежегодных событий",
     *    @OA\Parameter(
     *       name="filter[category]",
     *       in="query",
     *       description="Категория события",
     *       required=true,
     *       @OA\Schema(
     *          type="string",
     *          )
     *       ),
     *     @OA\Parameter(
     *        name="filter[name]",
     *        in="query",
     *        description="Название события",
     *        @OA\Schema(
     *           type="string",
     *       )
     *     ),
     *     @OA\Parameter(
     *        name="filter[trashed]",
     *        in="query",
     *        description="Показать удаленных(архивных) (with/only)",
     *        @OA\Schema(
     *           type="string",
     *           enum={"with","only"},
     *         )
     *      ),
     *     @OA\Parameter(
     *        name="filter[type]",
     *        in="query",
     *        description="Фильтр по типу событий",
     *        @OA\Schema(
     *           type="string",
     *           enum={"main","masterClass"},
     *         )
     *     ),
     *     @OA\Parameter(
     *        name="filter[year]",
     *        in="query",
     *        description="Фильтр по году",
     *        @OA\Schema(
     *           type="string",
     *         )
     *     ),
     *    @OA\Parameter(
     *       name="filter[status]",
     *       in="query",
     *       required=false,
     *       @OA\Schema(
     *          type="boolean",
     *          nullable=true,
     *          example=true
     *        )
     *    ),
     *   @OA\Response(
     *      response=200,
     *      description="Success",
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
     * @param ListEventRequest $request
     * @return EventCollection
     */
    public function index(ListEventRequest $request): EventCollection
    {
        $appData = $request->validated();
        return new EventCollection($this->eventService->list($appData));
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/events",
     *      operationId="CreateEvent",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|События"},
     *      summary="Создание нового события (выставки)",
     *      @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *        type="object",
     *        required={"name","description","place","type","year", "start_date","end_date","start_accepting_applications","end_accepting_applications"},
     *          @OA\Property(property="name", description="Основное название выставки", type="object",
     *             @OA\Property(property="ru", example="Супер выставка"),
     *             @OA\Property(property="en", example="Super")
     *          ),
     *          @OA\Property(property="description", description="Описание выставки", type="object",
     *             @OA\Property(property="ru", example="Приходите будет интересно"),
     *             @OA\Property(property="en", example="Some text")
     *          ),
     *          @OA\Property(property="slug",description="Уникальный слог", type="string",example="event_"),
     *          @OA\Property(property="sort_id",description="ID для костоный сортировки", type="integer",example=1),
     *          @OA\Property(property="place",description="Место (адрес) проведения", type="object",
     *             @OA\Property(property="ru",example="Моссква, БГД",type="string"),
     *             @OA\Property(property="en",example="Moscow",type="string"),
     *          ),
     *          @OA\Property(property="social_links",description="ССылки на соцсети", type="array",
     *             @OA\Items(
     *                   @OA\Property(property="title",description="Соцсеть", type="string"),
     *                   @OA\Property(property="url",description="Ссылка", type="string"),
     *                 ),
     *              example={{"title":"VK","url":"https://vk.com"}},
     *           ),
     *          @OA\Property(property="type",description="Тип события", type="string", example="main2025"),
     *          @OA\Property(property="year",description="год", type="string", example="2023"),
     *          @OA\Property(property="start_date",description="Открытие выставки", type="date", example="2023-03-15"),
     *          @OA\Property(property="end_date",description="Закрытие выставки", type="date", example="2023-03-23"),
     *          @OA\Property(property="status",description="Статус (true/false)", type="boolean", example="true"),
     *          @OA\Property(property="start_accepting_applications",description="начало приема заявок",type="string",example="2024-06-10T08:43:13"),
     *          @OA\Property(property="end_accepting_applications",description="Конец приема заявок",type="string",example="2024-07-11T08:43:13"),
     *          @OA\Property(property="locate",description="Язык", type="string", example="ru"),
     *          @OA\Property(property="event_type",description="Тип событие для программы", type="string", example="artForum"),
     *          @OA\Property(property="category",description="Катигория событие для программы", type="string", example="classic"),
     *       ),
     *     ),
     *     ),
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
     * @param StoreEventRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(StoreEventRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $data = $request->validated();
            return new ApiSuccessResponse(
                new  EventResource($this->eventService->create($data)),
                ['message' => 'Событие успешно создано'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при создании события',
                $exception
            );
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/events/show",
     *      operationId="GetEvent",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|События"},
     *      summary="Просмотр события (выставки)",
     *      @OA\Parameter(
     *        name="id",
     *        in="query",
     *        required=true,
     *        description="ID события",
     *        @OA\Schema(
     *           type="string",
     *      ),
     *   ),
     *
     *  @OA\Response(
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
     * @param ShowEventRequest $request
     * @return ApiSuccessResponse
     */
    public function show(ShowEventRequest $request): ApiSuccessResponse
    {
        $dataApp = $request->validated();
        return new ApiSuccessResponse(
            new  EventResource($this->eventService->show($dataApp['id'])),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/admin/events/{id}",
     *      operationId="UpdateEventApp",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|События"},
     *      summary="Редактирование события (выставки)",
     *     @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *     @OA\Parameter(
     *        name="category",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *            type="string"
     *       )
     *    ),
     *       @OA\RequestBody(
     *         required=true,
     *        @OA\MediaType(
     *         mediaType="application/vnd.api+json",
     *         @OA\Schema(
     *         type="object",
     *           @OA\Property(property="name", description="Основное название выставки", type="object",
     *              @OA\Property(property="ru", example="Супер выставка"),
     *              @OA\Property(property="en", example="Super")
     *           ),
     *           @OA\Property(property="description", description="Описание выставки", type="object",
     *              @OA\Property(property="ru", example="Приходите будет интересно"),
     *              @OA\Property(property="en", example="Some text")
     *           ),
     *           @OA\Property(property="slug",description="Уникальный слог", type="string",example="event_"),
     *           @OA\Property(property="sort_id",description="ID для костоный сортировки", type="integer",example=1),
     *           @OA\Property(property="place",description="Место (адрес) проведения", type="object",
     *              @OA\Property(property="ru",example="Моссква, БГД",type="string"),
     *              @OA\Property(property="en",example="Moscow",type="string"),
     *           ),
     *           @OA\Property(property="social_links",description="ССылки на соцсети", type="array",
     *              @OA\Items(
     *                    @OA\Property(property="title",description="Соцсеть", type="string"),
     *                    @OA\Property(property="url",description="Ссылка", type="string"),
     *                  ),
     *               example={{"title":"VK","url":"https://vk.com"}},
     *            ),
     *           @OA\Property(property="type",description="Тип события", type="string", example="main"),
     *           @OA\Property(property="year",description="год", type="string", example="2023"),
     *           @OA\Property(property="start_date",description="Открытие выставки", type="date", example="2023-03-15"),
     *           @OA\Property(property="end_date",description="Закрытие выставки", type="date", example="2023-03-23"),
     *           @OA\Property(property="status",description="Статус (true/false)", type="boolean", example="true"),
     *           @OA\Property(property="start_accepting_applications",description="начало приема заявок",type="string",example="2024-06-10T08:43:13"),
     *           @OA\Property(property="end_accepting_applications",description="Конец приема заявок",type="string",example="2024-07-11T08:43:13"),
     *           @OA\Property(property="locate",description="Язык", type="string", example="ru"),
     *           @OA\Property(property="event_type",description="Тип событие для программы", type="sringt", example="artForum"),
     *        ),
     *      ),
     *      ),
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
     * @param int $id
     * @param UpdateEventRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(int $id, UpdateEventRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $data = $request->validated();
            return new ApiSuccessResponse(
                new  EventResource($this->eventService->update($id, $data)),
                ['message' => 'Событие успешно обновлено'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при обновлении события',
                $exception
            );
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/admin/events/{id}",
     *      operationId="DelEvent",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|События"},
     *      summary="Полностью удаление события (выставки)",
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
     * @param int $id
     * @return ResourceNotFoundException|ApiSuccessResponse
     * @throws CustomException|Throwable
     */

    public function destroy(int $id): ResourceNotFoundException|ApiSuccessResponse
    {
        try {
            return new ApiSuccessResponse(
                $this->eventService->delete($id, EventService::DELETE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Delete(
     *       path="/api/v1/admin/events/archive/{id}",
     *       operationId="AdminEventArchive",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|События"},
     *       summary="Добавить события (выставки) в архив",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID события",
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
     * @throws CustomException|Throwable
     */
    public function archive(int $id): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            return new ApiSuccessResponse(
                $this->eventService->delete($id, EventService::ARCHIVE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Patch(
     *    path="/api/v1/admin/events/restore/{id}",
     *    operationId="RestoreEvent",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|События"},
     *    summary="Восстановить события (выставки) из архива ",
     *    @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       description="ID события",
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
     *               @OA\Property(property="data", type="boolean", example="true"),
     *               @OA\Property(property="metadata",type="object",
     *                  @OA\Property(property="message", example="Ok"),
     *               ),
     *             ),
     *          ),
     *      ),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=400, description="Bad Request"),
     *       @OA\Response(response=404,description="Not Found",
     *           @OA\JsonContent(
     *              @OA\Property(property="errors", type="array",
     *              @OA\Items(
     *                 @OA\Property(property="status", example="404"),
     *                 @OA\Property(property="detail", example="Such an event does not exist in the archive."),
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
        if ($this->eventService->checkData($id)) {
            $this->eventService->restore($id);
            return new ApiSuccessResponse(
                new ArtistResource($this->eventService->showById($id)),
                ['message' => 'успешно восстановлен'],
                ResponseAlias::HTTP_OK
            );
        }
        return new ApiErrorResponse(
            'Such an event does not exist in the archive.',
            null,
            ResponseAlias::HTTP_NOT_FOUND
        );
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/events/slots",
     *      operationId="EventSlots",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|События"},
     *      summary="Просмотр слотов привязанных событию (выставки)",
     *      @OA\Parameter(
     *        name="id",
     *        in="query",
     *        required=true,
     *        description="ID события",
     *        @OA\Schema(
     *           type="string",
     *      ),
     *   ),
     *
     *  @OA\Response(
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
     * @param ShowEventRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function slots(ShowEventRequest $request): ApiSuccessResponse
    {
        $dataApp = $request->validated();
        return new ApiSuccessResponse(
            $this->eventService->slots($dataApp['id']),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @throws Throwable
     * @throws CustomException
     */
    public function copyData(): void
    {
        $this->eventService->copyData();
    }

    /**
     * @param Request $request
     * @return Builder|Model|string
     * @throws CustomException
     */
    public function addBarCodes(Request $request): Builder|Model|string
    {
        return $this->eventService->addCodes($request->all());
    }
}
