<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\InformationForPlacement\InformationForPlacementListRequest;
use Admin\Services\InformationForPlacementService;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use OpenApi\Annotations as OA;


class InformationForPlacementController extends Controller
{
    public function __construct(public InformationForPlacementService $service)
    {
    }
    /**
     * @OA\Get(
     *    path="/api/v1/admin/information-placement/list",
     *    tags={"Admin|Информация для размещения"},
     *    security={{"bearerAuth":{}}},
     *    summary="Получит список информацию для размещения",
     *    operationId="AdminGetInformationForPlacements",
     *    @OA\Parameter(
     *             name="sort",
     *             in="query",
     *             description="Сортировка по поле",
     *             @OA\Schema(
     *                type="string",
     *               enum={"information_for_placements.id","-information_for_placements.id","information_for_placements.user_application_id","-information_for_placements.user_application_id",
     *                      "information_for_placements.type","-information_for_placements.type",
     *                      "information_for_placements.created_at","-information_for_placements.created_at",
     *                      "information_for_placements.updated_at","-information_for_placements.updated_at"
     *                     },
     *              )
     *          ),
     *        @OA\Parameter(
     *                name="page",
     *                in="query",
     *                description="Номер страницы",
     *                @OA\Schema(
     *                   type="integer",
     *                   example=1
     *               )
     *           ),
     *          @OA\Parameter(
     *              name="per_page",
     *              in="query",
     *              description="Количество элементов на странице",
     *              @OA\Schema(
     *                 type="integer",
     *                  example=10
     *               )
     *          ),
     *     @OA\Response(
     *      response=200,
     *       description="Success",
     *              @OA\MediaType(
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
     *)
     * @param InformationForPlacementListRequest $request
     * @return LengthAwarePaginator
     */
    public function list(InformationForPlacementListRequest $request): LengthAwarePaginator
    {
        $dataApp = $request->validated();
        return $this->service->list($dataApp);
    }
}
