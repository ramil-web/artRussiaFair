<?php

namespace Admin\Classic\Http\Controllers;

use Admin\Classic\Http\Requests\ClassicUserApplication\ListClassicUserApplicationRequest;
use Admin\Classic\Http\Requests\ClassicUserApplication\ShowClassicUserApplicationRequest;
use Admin\Classic\Http\Requests\ClassicUserApplication\UpdateClassicUserApplicationRequest;
use Admin\Classic\Http\Resources\ClassicUserApplication\ClassicUserApplicationCollection;
use Admin\Classic\Http\Resources\ClassicUserApplication\ClassicUserApplicationResource;
use Admin\Classic\Services\ClassicUserApplicationService;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use Cache;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ClassicUserApplicationController extends Controller
{
    public function __construct(public ClassicUserApplicationService $service)
    {
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/classic/applications",
     *    tags={"Admin|Classic|Заявки"},
     *    security={{"bearerAuth":{}}},
     *    summary="Список всех заявок",
     *    operationId="AdminClassicApp",
     *    @OA\Parameter(
     *       name="filter[id]",
     *       in="query",
     *       description="Фильтр по id",
     *       @OA\Schema(
     *          type="string",
     *          )
     *       ),
     *    @OA\Parameter(
     *       name="filter[type]",
     *       in="query",
     *       description="Фильтр по типу",
     *       @OA\Schema(
     *          type="string",
     *          enum={"gallery","artist"}
     *        )
     *    ),
     *    @OA\Parameter(
     *       name="filter[status]",
     *       in="query",
     *       description="Фильтр по статусу",
     *       @OA\Schema(
     *          type="string",
     *          enum={"new","pre_assessment","waiting","under_consideration","waitin_after_edit","confirmed","rejected","approved"},
     *        )
     *    ),
     *    @OA\Parameter(
     *       name="filter[representative_email]",
     *       in="query",
     *       description="Фильтр по email",
     *       @OA\Schema(
     *          type="string",
     *       )
     *    ),
     *    @OA\Parameter(
     *       name="filter[representative_city]",
     *       in="query",
     *       description="Фильтр по городу",
     *       @OA\Schema(
     *          type="string",
     *       )
     *     ),
     *    @OA\Parameter(
     *       name="filter[about_style]",
     *       in="query",
     *       description="Фильтр по cтиль",
     *       @OA\Schema(
     *          type="string",
     *        )
     *    ),
     *    @OA\Parameter(
     *       name="sort",
     *       in="query",
     *       description="Сортировка по поле",
     *       @OA\Schema(
     *          type="string",
     *          enum={"id","-id","user_id","-user_id","type","-type","status","-status","created_at","-created_at","updated_at","-updated_at","visualization_count","-visualization_count"},
     *        )
     *     ),
     *    @OA\Parameter(
     *       name="page",
     *       in="query",
     *       description="Номер страницы",
     *       @OA\Schema(
     *          type="integer",
     *          example=1
     *       )
     *    ),
     *    @OA\Parameter(
     *      name="per_page",
     *      in="query",
     *      description="Количество элементов на странице",
     *    @OA\Schema(
     *       type="integer",
     *       example=10
     *       )
     *    ),
     *    @OA\Response(
     *       response=200,
     *       description="Success",
     *       @OA\MediaType(
     *       mediaType="application/vnd.api+json",
     *      )
     *   ),
     *
     *   @OA\Response(
     *      response=401,
     *      description="Unauthenticated"
     *   ),
     *    @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *    @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *   @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     * @param ListClassicUserApplicationRequest $request
     * @return ClassicUserApplicationCollection
     */
    public function list(ListClassicUserApplicationRequest $request): ClassicUserApplicationCollection
    {
        Cache::put('classic_user_application_list', 'list');
        $dataApp = $request->validated();
        return new ClassicUserApplicationCollection($this->service->list($dataApp));
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/classic/application",
     *      operationId="GetClassicUserApp",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Classic|Заявки"},
     *      summary="Получить данные Заявки",
     *
     *     @OA\Parameter(
     *      name="id",
     *      in="query",
     *      required=true,
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
     * @param ShowClassicUserApplicationRequest $request
     * @return ApiSuccessResponse
     */
    public function show(ShowClassicUserApplicationRequest $request): ApiSuccessResponse
    {
        Cache::put('classic_user_application_list', __FUNCTION__);
        $data = $request->validated();
        return new ApiSuccessResponse(
            new ClassicUserApplicationResource($this->service->show($data)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Patch(
     *    path="/api/v1/admin/classic/application/update",
     *    operationId="UpdateClassicUserApp",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Classic|Заявки"},
     *    summary="Редактирование заявки(смена статуса)",
     *    @OA\Parameter(
     *      name="id",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="integer",
     *           example="1",
     *      )
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Cтатусы",
     *         @OA\Schema(
     *            type="string",
     *            enum={"pre_assessment","waiting","under_consideration","waiting_after_edit","confirmed","rejected","processing","approved"},
     *             example="confirmed"
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
     * @param UpdateClassicUserApplicationRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function update(UpdateClassicUserApplicationRequest $request): ApiSuccessResponse
    {
        $data = $request->validated();
        return new ApiSuccessResponse(
            new ClassicUserApplicationResource($this->service->update($data)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

}
