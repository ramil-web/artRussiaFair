<?php

namespace Lk\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Http\Request;
use Lk\Http\Requests\UserApplication\ListUserApplicationRequest;
use Lk\Http\Requests\UserApplication\StatusUserApplicationRequest;
use Lk\Http\Requests\UserApplication\StoreUserApplicationRequest;
use Lk\Http\Requests\UserApplication\UpdateUserApplicationRequest;
use Lk\Http\Resources\UserApplications\UserApplicationCollection;
use Lk\Http\Resources\UserApplications\UserApplicationResource;
use Lk\Services\UserApplicationService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class UserApplicationController extends Controller
{

    public function __construct(public UserApplicationService $userApplicationService)
    {
    }

    /**
     * @OA\Get(
     *    path="/api/v1/lk/applications",
     *    tags={"Lk|Заявки"},
     *    security={{"bearerAuth":{}}},
     *    summary="Список всех заявок пользователя",
     *    operationId="Lk.app",
     *    @OA\Parameter(
     *         name="filter[category]",
     *         in="query",
     *         description="Фильтр по category пока modern|classic",
     *         required=true,
     *         @OA\Schema(
     *            type="string",
     *                )
     *            ),
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
    public function index(ListUserApplicationRequest $request): UserApplicationCollection
    {
        $appData = $request->validated();
        return new UserApplicationCollection($this->userApplicationService->list($appData));
    }

    /**
     * @OA\Post(
     *      path="/api/v1/lk/applications",
     *      operationId="CreateUserApp",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Заявки"},
     *      summary="Заполнение(создание) заявки",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *          type="object",
     *          required={"type","representative_name","representative_surname","representative_email",
     *          "representative_phone","representative_city","about_style","about_description","event_id"},
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
     *          @OA\Property(property="event_id",description="ID заявлении", type="integer", example=1),
     *          @OA\Property(property="education", description="Образование", type="string",example="МГУ"),
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
     *
     * @throws CustomException
     */
    public function store(StoreUserApplicationRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        $data = $request->validated();
        $checkApp = $this->userApplicationService->checkApp($data['event_id']);
        if ($checkApp) {
            try {
                $userApplication = $this->userApplicationService->create($data);
                return new ApiSuccessResponse(
                    new  UserApplicationResource($userApplication),
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
     * @OA\Get(
     *      path="/api/v1/lk/applications/{id}",
     *      operationId="LkGetUserApp",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Заявки"},
     *      summary="Просмотр заявки",
     *     @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *
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
    public function show(int $id, Request $request): ApiSuccessResponse
    {
        \Cache::put('user_application_list', '');
        $app_user = new  UserApplicationResource($this->userApplicationService->show($id, $request));
        return new ApiSuccessResponse(
            $app_user,
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/lk/applications/{id}",
     *      operationId="LkUpdateUserApp",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Заявки"},
     *      summary="Редактирование заявки",
     *      @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID заявки",
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *         @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *      type="object",
     *      required={"locate"},
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
     *          @OA\Property(property="event_id",description="ID события", type="number", example=1),
     *          @OA\Property(property="education", description="Образование", type="srtring", example="МГУ"),
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
     * @param int $id
     * @param UpdateUserApplicationRequest $request
     * @return ApiErrorResponse|ApiSuccessResponse
     * @throws CustomException
     */
    public function update(int $id, UpdateUserApplicationRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        $dataApp = $request->validated();
        if (!$this->userApplicationService->checkStatus($id)) {
            return new ApiErrorResponse(
                'Отклоненные заявки нельзя редактировать',
            );
        }

        if (!$this->userApplicationService->checkIsNew($id)) {
            return new ApiErrorResponse(
                'Отклонено! Заявка доступна на редактирование 24 часа',
            );
        }

        return new ApiSuccessResponse(
            new  UserApplicationResource($this->userApplicationService->update($id, $dataApp)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *    path="/api/v1/lk/getstatus",
     *    operationId="LkStatusUserApp",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Заявки"},
     *    summary="Получение статуса активной заявки",
     *    @OA\Parameter(
     *        name="category",
     *        in="query",
     *        description="Категория событие пока modern|classic",
     *        required=true,
     *        @OA\Schema(
     *           type="string",
     *                 )
     *         ),
     *    @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     *    @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *    @OA\Response(
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
    public function getStatus(StatusUserApplicationRequest $request): ApiSuccessResponse
    {
        $data = $request->validated();
        $status = $this->userApplicationService->getStatus($data['category']);
        return new ApiSuccessResponse(
            $status,
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }
}
