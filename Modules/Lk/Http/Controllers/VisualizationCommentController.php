<?php

namespace Lk\Http\Controllers;

use Admin\Http\Resources\VisualizationComment\VisualizationCommentResource;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use Lk\Http\Requests\Visualization\VisualizationCommentListRequest;
use Lk\Http\Requests\Visualization\VisualizationCommentShowRequest;
use Lk\Http\Resources\Visualization\VisualizationCommentCollection;
use Lk\Services\VisualizationCommentService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class VisualizationCommentController extends Controller
{

    public function __construct(public VisualizationCommentService $service)
    {
    }


    /**
     * @OA\Get(
     *     path="/api/v1/lk/applications/visualization-comment/show",
     *     operationId="LkgetVisualizationComment",
     *     security={{"bearerAuth":{}}},
     *     tags={"Lk|Заявки|Визуализация|Комментарии менеджера"},
     *     summary="Получить(просмотр) коммментария",
     *     @OA\Parameter(
     *        name="user_application_id",
     *        in="query",
     *        required=false,
     *        description="ID Заявки",
     *        @OA\Schema(
     *            type="integer"
     *       )
     *    ),
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        required=true,
     *        description="ID Комментария",
     *        @OA\Schema(
     *             type="integer"
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
     * @param VisualizationCommentShowRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function show(VisualizationCommentShowRequest $request): ApiSuccessResponse
    {
        $appData = $request->validated();
        return new ApiSuccessResponse(
            new  VisualizationCommentResource($this->service->show($appData['id'])),
            ['message' => 'Ok'],
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/lk/applications/visualization-comment/list",
     *     operationId="LklistVisualizationComment",
     *     security={{"bearerAuth":{}}},
     *     tags={"Lk|Заявки|Визуализация|Комментарии менеджера"},
     *     summary="Получить коммментарии к визуалтзации",
     *     @OA\Parameter(
     *        name="user_application_id",
     *        in="query",
     *        required=true,
     *        description="ID Заявки",
     *        @OA\Schema(
     *            type="integer"
     *       )
     *    ),
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
     * @param VisualizationCommentListRequest $request
     * @return ApiSuccessResponse
     */
    public function list(VisualizationCommentListRequest $request): ApiSuccessResponse
    {
        $appData = $request->validated();
        return new ApiSuccessResponse(
            new  VisualizationCommentCollection($this->service->list($appData)),
            ['message' => 'Ok'],
            Response::HTTP_OK
        );
    }
}
