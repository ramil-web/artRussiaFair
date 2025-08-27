<?php

namespace Admin\Classic\Http\Controllers;

use Admin\Classic\Http\Requests\ClassicEvent\ListClassicEventRequest;
use Admin\Classic\Http\Requests\ClassicEvent\ShowClassicEventRequest;
use Admin\Classic\Http\Requests\ClassicEvent\StoreClassicEventRequest;
use Admin\Classic\Http\Requests\ClassicEvent\UpdateClassicEventRequest;
use Admin\Classic\Http\Resources\ClassicEvent\ClassicEventCollection;
use Admin\Classic\Http\Resources\ClassicEvent\ClassicEventResource;
use Admin\Classic\Services\ClassicEventService;
use App\Exceptions\CustomException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class ClassicEventController extends Controller
{
    public function __construct(public ClassicEventService $service)
    {
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/classic/event/store",
     *      operationId="ClassicCreateEvent",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Classic|События"},
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
     *          @OA\Property(property="year",description="год", type="string", example="2023"),
     *          @OA\Property(property="start_date",description="Открытие выставки", type="date", example="2025-03-15"),
     *          @OA\Property(property="end_date",description="Закрытие выставки", type="date", example="2026-03-23"),
     *          @OA\Property(property="status",description="Статус (true/false)", type="boolean", example="true"),
     *          @OA\Property(property="start_accepting_applications",description="начало приема заявок",type="string",example="2025-06-10T08:43:13"),
     *          @OA\Property(property="end_accepting_applications",description="Конец приема заявок",type="string",example="2026-07-11T08:43:13"),
     *          @OA\Property(property="locate",description="Язык", type="string", example="ru"),
     *          @OA\Property(property="event_type",description="Тип событие для программы", type="string", example="main26"),
     *       ),
     *     ),
     *     ),
     *     @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *        )
     *     ),
     *     @OA\Response(
     *        response=401,
     *        description="Unauthenticated"
     *     ),
     *    @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *    ),
     *    @OA\Response(
     *      response=404,
     *      description="not found"
     *    ),
     *    @OA\Response(
     *       response=403,
     *       description="Forbidden"
     *      )
     *   )
     * @param StoreClassicEventRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(StoreClassicEventRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $data = $request->validated();
            return new ApiSuccessResponse(
                new  ClassicEventResource($this->service->create($data)),
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
     *    path="/api/v1/admin/classic/event",
     *    operationId="ClassicShowEvent",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Classic|События"},
     *    summary="Просмотр события (выставки)",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
     *       @OA\Schema(
     *            type="integer"
     *       )
     *    ),
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
     * @param ShowClassicEventRequest $request
     * @return ApiSuccessResponse
     */
    public function show(ShowClassicEventRequest $request): ApiSuccessResponse
    {
        $data = $request->validated();
        return new ApiSuccessResponse(
            new  ClassicEventResource($this->service->show($data['id'])),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/classic/events",
     *    operationId="ListClassicEvent",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Classic|События"},
     *    summary="Получить список ежегодных событий",
     *    @OA\Parameter(
     *       name="filter[name]",
     *       in="query",
     *       description="Название события",
     *       @OA\Schema(
     *          type="string",
     *        )
     *     ),
     *     @OA\Parameter(
     *        name="filter[trashed]",
     *        in="query",
     *        description="Показать удаленных(архивных) (with/only)",
     *        @OA\Schema(
     *           type="string",
     *           enum={"with","only"},
     *          )
     *      ),
     *     @OA\Parameter(
     *         name="filter[event_type]",
     *         in="query",
     *         description="Фильтр по типу событий",
     *         @OA\Schema(
     *            type="string",
     *            enum={"artForum","masterClassAdult","masterClassChild","expertTable"},
     *         )
     *     ),
     *      @OA\Parameter(
     *          name="filter[year]",
     *          in="query",
     *          description="Фильтр по году",
     *          @OA\Schema(
     *                type="string",
     *              )
     *      ),
     *     @OA\Parameter(
     *         name="filter[status]",
     *         in="query",
     *         description="Фильтр по статусу (активное или прошедшее)",
     *         @OA\Schema(
     *               type="boolean",
     *                   )
     *     ),
     *      @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          description="Сортировка по поле",
     *          @OA\Schema(
     *             type="string",
     *             enum={"id","sort_id","name","created_at", "updated_at"},
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
     * @param ListClassicEventRequest $request
     * @return ApiErrorResponse|ClassicEventCollection
     */
    public function list(ListClassicEventRequest $request): ApiErrorResponse|ClassicEventCollection
    {
        $data = $request->validated();
        return new  ClassicEventCollection($this->service->list($data));
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/admin/classic/event/update",
     *     operationId="UpdateClassicEventApp",
     *     security={{"bearerAuth":{}}},
     *     tags={"Admin|Classic|События"},
     *     summary="Редактирование события (выставки)",
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *           type="integer"
     *      )
     *      ),
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
     *   @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     * @param UpdateClassicEventRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(UpdateClassicEventRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $data = $request->validated();
            return new ApiSuccessResponse(
                new  ClassicEventResource($this->service->update($data)),
                ['message' => 'Событие успешно обновлено'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при обновлении событие',
                $exception
            );
        }
    }

    /**
     * @OA\Delete(
     *    path="/api/v1/admin/classic/event/archive",
     *    operationId="ArchiveClassicEventApp",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Classic|События"},
     *    summary="Добавить события (выставки) в архив",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
     *       @OA\Schema(
     *          type="integer"
     *       )
     *     ),
     *    @OA\Response(
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
     * @param ShowClassicEventRequest $request
     * @return ResourceNotFoundException|ApiSuccessResponse
     * @throws CustomException
     * @throws Throwable
     */
    public function archive(ShowClassicEventRequest $request): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            $data = $request->validated();
            return new ApiSuccessResponse(
                $this->service->delete($data['id'], ClassicEventService::ARCHIVE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Patch(
     *    path="/api/v1/admin/classic/event/restore",
     *    operationId="RestoreClassicEventApp",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Classic|События"},
     *    summary="Восстановить события (выставки) из архива ",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
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
     * @param ShowClassicEventRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     * @throws CustomException
     */
    public function restore(ShowClassicEventRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        $data = $request->validated();
        if ($this->service->checkData($data['id'])) {
            $this->service->restore($data['id']);
            return new ApiSuccessResponse(
                new ClassicEventResource($this->service->show($data['id'])),
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
     * @OA\Delete(
     *     path="/api/v1/admin/classic/event/delete",
     *     operationId="DeleteClassicEventApp",
     *     security={{"bearerAuth":{}}},
     *     tags={"Admin|Classic|События"},
     *     summary="Полностью удаление события (выставки)",
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
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
     * @param ShowClassicEventRequest $request
     * @return ApiSuccessResponse|CustomException
     */

    public function destroy(ShowClassicEventRequest $request): ApiSuccessResponse|CustomException
    {
        try {
            $data = $request->validated();
            return new ApiSuccessResponse(
                $this->service->delete($data['id'], ClassicEventService::DELETE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException|Throwable|CustomException $e) {
            return new CustomException($e);
        }
    }
}
