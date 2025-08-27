<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\UserApplication\ListUserApplicationRequest;
use Admin\Http\Requests\UserApplication\UpdateUserApplicationRequest;
use Admin\Http\Requests\UserApplication\VisitorUserApplicationRequest;
use Admin\Http\Resources\UserApplications\UserApplicationCollection;
use Admin\Http\Resources\UserApplications\UserApplicationResource;
use Admin\Services\UserApplicationService;
use App\Exceptions\CustomException;
use App\Exports\UserApplicationExport;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use Excel;
use Exception;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserApplicationController extends Controller
{

    public function __construct(public UserApplicationService $userApplicationService)
    {}

    /**
     * @OA\Get(
     *    path="/api/v1/admin/applications",
     *    tags={"Admin|Заявки"},
     *    security={{"bearerAuth":{}}},
     *    summary="Список всех заявок",
     *    operationId="Admin.app",
     *    @OA\Parameter(
     *        name="filter[category]",
     *        in="query",
     *        description="Фильтр по category",
     *        required=true,
     *        @OA\Schema(
     *           type="string",
     *               )
     *           ),
     *     @OA\Parameter(
     *        name="filter[visualization]",
     *        in="query",
     *        description="Фильтр по визуализации",
     *        @OA\Schema(
     *           type="string",
     *           enum={"with","without","only"}
     *              )
     *        ),
     *     @OA\Parameter(
     *        name="filter[id]",
     *        in="query",
     *        description="Фильтр по id",
     *        @OA\Schema(
     *           type="string",
     *          )
     *     ),
     *    @OA\Parameter(
     *       name="filter[type]",
     *       in="query",
     *       description="Фильтр по типу",
     *       @OA\Schema(
     *          type="string",
     *          enum={"gallery","artist"}
     *           )
     *      ),
     *     @OA\Parameter(
     *        name="filter[status]",
     *        in="query",
     *        description="Фильтр по статусу",
     *        @OA\Schema(
     *               type="string",
     *               enum={"new","pre_assessment","waiting","under_consideration","waitin_after_edit","confirmed","rejected","approved"},
     *             )
     *         ),
     *    @OA\Parameter(
     *       name="filter[representative_email]",
     *       in="query",
     *       description="Фильтр по email",
     *       @OA\Schema(
     *           type="string",
     *              )
     *       ),
     *     @OA\Parameter(
     *        name="filter[representative_city]",
     *        in="query",
     *        description="Фильтр по городу",
     *        @OA\Schema(
     *                type="string",
     *              )
     *          ),
     *     @OA\Parameter(
     *         name="filter[about_style]",
     *         in="query",
     *         description="Фильтр по cтиль",
     *         @OA\Schema(
     *                type="string",
     *              )
     *          ),
     *     @OA\Parameter(
     *        name="sort",
     *        in="query",
     *        description="Сортировка по поле",
     *        @OA\Schema(
     *              type="string",
     *              enum={"id","-id","user_id","-user_id","type","-type","status","-status","created_at","-created_at","updated_at","-updated_at","visualization_count","-visualization_count"},
     *            )
     *        ),
     *     @OA\Parameter(
     *        name="page",
     *        in="query",
     *        description="Номер страницы",
     *        @OA\Schema(
     *           type="integer",
     *           example=1
     *             )
     *         ),
     *     @OA\Parameter(
     *        name="per_page",
     *        in="query",
     *        description="Количество элементов на странице",
     *        @OA\Schema(
     *               type="integer",
     *                example=10
     *             )
     *        ),
     *     @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *    ),
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
     *   @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     */
    public function index(ListUserApplicationRequest $request): UserApplicationCollection
    {
        \Cache::put('user_application_list', 'list');
        $dataApp = $request->validated();
        return new UserApplicationCollection($this->userApplicationService->list($dataApp));
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/applications/{id}",
     *      operationId="GetUserApp",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Заявки"},
     *      summary="Получить данные Заявки",
     *
     *     @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     *
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
        \Cache::put('user_application_list', __FUNCTION__);
        return new ApiSuccessResponse(
            new UserApplicationResource($this->userApplicationService->show($id, $request)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/admin/applications/{id}",
     *      operationId="UpdateUserApp",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Заявки"},
     *      summary="Редактирование заявки(смена статуса)",
     *
     *     @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *
     *      @OA\Schema(
     *           type="integer",
     *           example="1",
     *      )
     *     ),
     *     @OA\Parameter(
     *          name="status",
     *          in="query",
     *          description="Cтатусы",
     *          @OA\Schema(
     *                type="string",
     *                enum={"pre_assessment","waiting","under_consideration","waiting_after_edit","confirmed","rejected","processing","approved"},
     *                example="confirmed"
     *          ),
     *     ),
     *     @OA\RequestBody(
     *        required=false,
     *        @OA\MediaType(
     *         mediaType="application/vnd.api+json",
     *      ),
     *     ),
     *    @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *       ),
     *    ),
     *
     *    @OA\Response(response=401, description="Unauthenticated"),
     *    @OA\Response(response=400, description="Bad Request"),
     *    @OA\Response(response=404,description="not found"),
     *    @OA\Response(response=403,description="Forbidden")
     * )
     *
     * @param int $id
     * @param UpdateUserApplicationRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function update(int $id, UpdateUserApplicationRequest $request): ApiSuccessResponse
    {
        $dataApp = $request->validated();
        return new ApiSuccessResponse(
            new UserApplicationResource($this->userApplicationService->updateFromArray($id, $dataApp)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/admin/applications/visitor",
     *      operationId="AddVisitorToUserApp",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Заявки|Визитор"},
     *      summary="Добавляет поситителя в заявку",
     *      @OA\Parameter(
     *      name="user_application_id",
     *      in="query",
     *      required=true,
     *      description="ID заявки",
     *      @OA\Schema(
     *           type="integer",
     *           example="1",
     *      )
     *     ),
     *     @OA\Parameter(
     *       name="user_id",
     *       in="query",
     *       required=true,
     *       description="ID прользователя",
     *       @OA\Schema(
     *            type="integer",
     *            example="1",
     *       )
     *      ),
     *      @OA\Parameter(
     *        name="email",
     *        in="query",
     *        required=true,
     *        description="Email прользователя",
     *        @OA\Schema(
     *             type="string",
     *             example="admin@gmail.com",
     *        )
     *       ),
     *     @OA\Parameter(
     *        name="role",
     *        in="query",
     *        required=true,
     *        description="Роль прользователя",
     *        @OA\Schema(
     *             type="string",
     *             example="admin",
     *        )
     *       ),
     *    @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *       ),
     *    ),
     *
     *    @OA\Response(response=401, description="Unauthenticated"),
     *    @OA\Response(response=400, description="Bad Request"),
     *    @OA\Response(response=404,description="not found"),
     *    @OA\Response(response=403,description="Forbidden")
     * )
     *
     * @param VisitorUserApplicationRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function visitor(VisitorUserApplicationRequest $request): ApiSuccessResponse
    {
        $dataApp = $request->validated();
        return new ApiSuccessResponse(
            new UserApplicationResource($this->userApplicationService->addVisitor($dataApp)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/application/export",
     *     operationId="exportUserApplications",
     *     security={{"bearerAuth":{}}},
     *     tags={"Admin|Заявки|Выгрузка"},
     *     summary="Экспорт слотов",
     *    @OA\Parameter(
     *         name="filter[category]",
     *         in="query",
     *         description="Фильтр по category",
     *         required=true,
     *         @OA\Schema(
     *            type="string",
     *                )
     *            ),
     *     @OA\Parameter(
     *           name="filter[visualization]",
     *           in="query",
     *           description="Фильтр по визуализации",
     *           @OA\Schema(
     *               type="string",
     *                enum={"with","without","only"}
     *               )
     *           ),
     *       @OA\Parameter(
     *          name="filter[id]",
     *          in="query",
     *          description="Фильтр по id",
     *          @OA\Schema(
     *                type="string",
     *              )
     *          ),
     *      @OA\Parameter(
     *           name="filter[type]",
     *           in="query",
     *           description="Фильтр по типу",
     *           @OA\Schema(
     *                 type="string",
     *                 enum={"gallery","artist"}
     *               )
     *           ),
     *       @OA\Parameter(
     *          name="filter[status]",
     *          in="query",
     *          description="Фильтр по статусу",
     *
     *              @OA\Schema(
     *                type="string",
     *                enum={"new","pre_assessment","waiting","under_consideration","waitin_after_edit","confirmed","rejected","approved"},
     *              )
     *          ),
     *
     *      @OA\Parameter(
     *           name="filter[representative_email]",
     *           in="query",
     *           description="Фильтр по email",
     *
     *           @OA\Schema(
     *                 type="string",
     *               )
     *           ),
     *
     *      @OA\Parameter(
     *           name="filter[representative_city]",
     *           in="query",
     *           description="Фильтр по городу",
     *
     *           @OA\Schema(
     *                 type="string",
     *               )
     *           ),
     *
     *      @OA\Parameter(
     *           name="filter[about_style]",
     *           in="query",
     *           description="Фильтр по cтиль",
     *
     *           @OA\Schema(
     *                 type="string",
     *               )
     *           ),
     *           @OA\Parameter(
     *            name="sort",
     *            in="query",
     *            description="Сортировка по поле",
     *            @OA\Schema(
     *               type="string",
     *               enum={"id","-id","user_id","-user_id","type","-type","status","-status","created_at","-created_at","updated_at","-updated_at","visualization_count","-visualization_count"},
     *             )
     *         ),
     *       @OA\Parameter(
     *               name="page",
     *               in="query",
     *               description="Номер страницы",
     *               @OA\Schema(
     *                  type="integer",
     *                  example=""
     *              )
     *          ),
     *         @OA\Parameter(
     *             name="per_page",
     *             in="query",
     *             description="Количество элементов на странице",
     *             @OA\Schema(
     *                type="integer",
     *                 example=""
     *              )
     *         ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *              @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                  property="data",
     *                  type="string",
     *                  example="C:\\xampp\\htdocs\\newapiartrussiafair\\storage\\/app//time-slots/time-slots-1701685838.xlsx"
     *                 ),
     *               @OA\Property(property="metadata", type="object",
     *                @OA\Property(property="message", type="string", example="Ok"),
     *               ),
     *              ),
     *            ),
     *          ),
     *     @OA\Response(response=401,description="Unauthenticated"),
     *     @OA\Response(response=400,description="Bad Request"),
     *     @OA\Response(response=404,description="not found"),
     *     @OA\Response(response=403,description="Forbidden",
     *          @OA\JsonContent(
     *             @OA\Property(property="errors", type="array",
     *                 @OA\Items(
     *                    @OA\Property(property="status", example="403"),
     *                    @OA\Property(property="detail", example="User does not have the right roles.")
     *                 ),
     *             ),
     *          ),
     *      ),
     *     @OA\Response(response=500,description="Server error")
     * )
     * @param ListUserApplicationRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function export(ListUserApplicationRequest $request): ApiSuccessResponse
    {
        try {
            $appData = $request->validated();
            $export = new UserApplicationExport($appData, $this->userApplicationService);
            $fileName = '/user_applications/Заявки-' . date('Y-m-d_H-i-s') . '.xlsx';
            Excel::store($export, $fileName);
            $link = storage_path('/app/' . $fileName);
            return new ApiSuccessResponse($link, ['message' => 'Ok'], ResponseAlias::HTTP_OK);
        } catch (Exception|\PhpOffice\PhpSpreadsheet\Exception $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }
}
