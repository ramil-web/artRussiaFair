<?php

namespace Lk\Classic\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Lk\Classic\Http\Requests\ClassicUserApplication\ListClassicUserApplicationRequest;
use Lk\Classic\Http\Requests\ClassicUserApplication\ShowClassicUserApplicationRequest;
use Lk\Classic\Http\Requests\ClassicUserApplication\StoreClassicUserApplicationRequest;
use Lk\Classic\Http\Requests\ClassicUserApplication\UpdateClassicUserApplicationRequest;
use Lk\Classic\Http\Resources\ClassicUserApplication\ClassicUserApplicationResource;
use Lk\Classic\Services\ClassicUserApplicationService;
use Lk\Http\Resources\UserApplications\UserApplicationCollection;
use Lk\Http\Resources\UserApplications\UserApplicationResource;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class ClassicUserApplicationController extends Controller
{
    public function __construct(public ClassicUserApplicationService $service)
    {
    }

    /**
     * @OA\Get(
     *    path="/api/v1/lk/classic/application",
     *    operationId="LkClassicGetUserApp",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Classic|Заявки"},
     *    summary="Просмотр заявки",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
     *       @OA\Schema(
     *           type="integer"
     *        )
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
     **/
    public function show(ShowClassicUserApplicationRequest $request): ApiSuccessResponse
    {
        \Cache::put('classic_user_application_list', '');
        $data = $request->validated();
        $userApp = new  ClassicUserApplicationResource($this->service->show($data));
        return new ApiSuccessResponse(
            $userApp,
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *      path="/api/v1/lk/classic/applications",
     *      tags={"Lk|Classic|Заявки"},
     *      security={{"bearerAuth":{}}},
     *      summary="Список всех заявок пользователя",
     *      operationId="LkClassicApp",
     *      @OA\Parameter(
     *         name="filter[id]",
     *         in="query",
     *         description="Фильтр по id",
     *         @OA\Schema(
     *               type="string",
     *             )
     *         ),
     *      @OA\Parameter(
     *         name="filter[status]",
     *         in="query",
     *         description="Фильтр по статусу",
     *             @OA\Schema(
     *               type="string",
     *               enum={"new","waiting_after_edit","under_consideration","approved","contirmed","rejected"},
     *             )
     *         ),
     * @OA\Response(
     *       response=200,
     *       description="Success",
     *              @OA\MediaType(
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
     *)
     */
    public function index(ListClassicUserApplicationRequest $request): UserApplicationCollection
    {
        $data = $request->validated();
        return new UserApplicationCollection($this->service->list($data));
    }

    /**
     * @OA\Post(
     *      path="/api/v1/lk/classic/application/store",
     *      operationId="CreateClassicUserApp",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Classic|Заявки"},
     *      summary="Заполнение(создание) заявки",
     *      @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *      type="object",
     *     required={"type","representative_name","representative_surname","representative_email",
     *     "representative_phone","representative_city","about_style","about_description","event_id"},
     *          @OA\Property(property="locate",description="Язык(обязательно)", type="string",  example="ru"),
     *          @OA\Property(property="type",description="Тип галерея или художник",type="string",enum={"gallery","artist"},example="gallery"),
     *          @OA\Property(property="name_gallery",description="Название галереи",type="string",example="Галерея Пупкина"),
     *          @OA\Property(property="representative_name",description="Имя представителя",type="string",example="Иван"),
     *          @OA\Property(property="representative_surname",description="Фамилия представителя",type="string",example="Пупкин"),
     *          @OA\Property(property="representative_email",description="email представителя",type="string",example="pupkin@mail.ru"),
     *          @OA\Property(property="representative_phone",description="телефон представителя",type="string",example="+79999999999"),
     *          @OA\Property(property="representative_city",description="город представителя",type="string",example="Москва"),
     *          @OA\Property(property="about_style",description="стиль ", type="string", example="Уличное искусство"),
     *          @OA\Property(property="about_description",description="краткое описание",type="string",example="Я художник, я так вижу"),
     *          @OA\Property(property="status",description="Статус заявлении", type="string", example="new"),
     *          @OA\Property(property="classic_event_id",description="ID заявлении", type="integer", example=1),
     *          @OA\Property(property="other_fair",description="Другие выставки", type="array",
     *             @OA\Items(
     *                   @OA\Property(property="year",description="Дата участия", type="string"),
     *                   @OA\Property(property="title",description="Названеи", type="string"),
     *                   @OA\Property(property="city",description="Город участия", type="string"),
     *                 ),
     *          example={{"data":"20.03.2021","title":"Супервыставка","city":"Москва"},{"data":"20.03.2021","title":"Супервыставка","city":"Москва"}},
     *        ),
     *        @OA\Property(property="social_links",description="ССылки на соцсети", type="array",
     *             @OA\Items(
     *                   @OA\Property(property="title",description="Соцсеть", type="string"),
     *                   @OA\Property(property="url",description="Ссылка", type="string"),
     *
     *                 ),
     *              example={{"title":"VK","url":"http://vk.com"}},
     *         ),
     *
     *      @OA\Property(property="image",description="Примеры работ с описанием", type="array",
     *             @OA\Items(
     *                   @OA\Property(property="url",description="ссылка на изображение", type="string"),
     *                    @OA\Property(property="title",description="Соцсеть", type="string"),
     *                   @OA\Property(property="description",description="Описание", type="string"),
     *                    ),
     *          example={{"url":"/upload/img1.jpg","title":"Название1","description":"Картина №1"},{"url":"/upload/img2.jpg","title":"Название2","description":"Статуя №1"}},
     *        ),
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
     **/
    public function store(StoreClassicUserApplicationRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        $checkApp = $this->service->checkApp();
        if ($checkApp) {
            try {
                $data = $request->validated();
                $userApplication = $this->service->create($data);
                return new ApiSuccessResponse(
                    new  ClassicUserApplicationResource($userApplication),
                    ['message' => 'Заявка успешно создана'],
                    ResponseAlias::HTTP_OK
                );
            } catch (Throwable $exception) {
                return new ApiErrorResponse(
                    'Ошибка при создании заявки',
                    $exception
                );
            }
        }
        return new ApiErrorResponse(
            'У вас уже есть активная заявка',
        );
    }

    /**
     * @OA\Patch(
     *    path="/api/v1/lk/classic/application/update",
     *    operationId="LkClassicUpdateUserApp",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Classic|Заявки"},
     *    summary="Редактирование заявки",
     *    @OA\Parameter(
     *        name="id",
     *        in="query",
     *        required=true,
     *        description="ID заявки",
     *        @OA\Schema(
     *           type="integer"
     *        )
     *      ),
     *      @OA\RequestBody(
     *      required=true,
     *          @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *          type="object",
     *          required={"locate"},
     *          @OA\Property(property="locate",description="Язык(обязательно)", type="string",  example="ru"),
     *          @OA\Property(property="name_gallery",description="Название галереи", type="string",  example="Галерея Пупкина"),
     *          @OA\Property(property="representative_name",description="Имя представителя", type="string", example=" Иван"),
     *          @OA\Property(property="representative_surname",description="Фамилия представителя", type="string", example=" Пупкин"),
     *          @OA\Property(property="representative_email",description="email представителя", type="string", example="pupkin@mail.ru"),
     *          @OA\Property(property="representative_phone",description="телефон представителя", type="string", example="+79999999999"),
     *          @OA\Property(property="representative_city",description="город представителя", type="string", example="Москва"),
     *          @OA\Property(property="about_style",description="стиль ", type="string", example="Уличное искусство"),
     *          @OA\Property(property="about_description",description="краткое описание", type="string", example="Я художник, я так вижу"),
     *          @OA\Property(property="type",description="тип", type="string", example="gallery"),
     *          @OA\Property(property="classic_event_id",description="ID события", type="number", example=1),
     *          @OA\Property(property="other_fair",description="Другие выставки", type="array",
     *             @OA\Items(
     *                   @OA\Property(property="year",description="Дата участия", type="string",example=""),
     *                   @OA\Property(property="title",description="Названеи", type="string",example=""),
     *                   @OA\Property(property="city",description="Город участия", type="string",example=""),
     *                 ),
     *          example={{"data":"2021","title":"Супервыставка","city":"Москва"},{"data":"2021","title":"Супервыставка","city":"Москва"}},
     *        ),
     *        @OA\Property(property="social_links",description="ССылки на соцсети", type="array",
     *             @OA\Items(
     *                   @OA\Property(property="title",description="Соцсеть", type="string"),
     *                   @OA\Property(property="url",description="Ссылка", type="string"),
     *
     *                 ),
     *              example={{"title":"VK","url":"http://vk.com"}},
     *         ),
     *
     *      @OA\Property(property="image",description="Примеры работ с описанием", type="array",
     *             @OA\Items(
     *                   @OA\Property(property="url",description="ссылка на изображение", type="string"),
     *                   @OA\Property(property="title",description="Соцсеть", type="string"),
     *                   @OA\Property(property="description",description="Описание", type="string"),
     *                    ),
     *          example={{"url":"/upload/img1.jpg","title":"Название1","description":"Картина №1"},{"url":"/upload/img2.jpg","title":"Название2","description":"Статуя №1"}},
     *        ),
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
     * @param UpdateClassicUserApplicationRequest $request
     * @return ApiErrorResponse|ApiSuccessResponse
     * @throws CustomException
     */
    public function update(UpdateClassicUserApplicationRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        $data = $request->validated();
        if (!$this->service->checkStatus($data['id'])) {
            return new ApiErrorResponse(
                'Отклоненные заявки нельзя редактировать',
            );
        }

        if (!$this->service->checkIsNew($data['id'])) {
            return new ApiErrorResponse(
                'Отклонено! Заявка доступна на редактирование 24 часа',
            );
        }

        return new ApiSuccessResponse(
            new  UserApplicationResource($this->service->update($data)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *    path="/api/v1/lk/classic/getStatus",
     *    operationId="LkClassicStatusUserApp",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Classic|Заявки"},
     *    summary="Получение статуса активной заявки",
     *    @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *    ),
     *    @OA\Response(
     *       response=401,
     *       description="Unauthenticated"
     *    ),
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
     * @return ApiSuccessResponse
     */
    public function status(): ApiSuccessResponse
    {
        $status = $this->service->getStatus();
        return new ApiSuccessResponse(
            $status,
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }
}
