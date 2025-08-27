<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\VisualizationAssessment\VisualizationAssessmentListRequest;
use Admin\Http\Requests\VisualizationAssessment\VisualizationAssessmentShowRequest;
use Admin\Http\Requests\VisualizationAssessment\VisualizationAssessmentSoreRequest;
use Admin\Http\Requests\VisualizationAssessment\VisualizationAssessmentUpdateRequest;
use Admin\Http\Resources\VisualisationAssessment\VisualizationAssessmentCollection;
use Admin\Http\Resources\VisualisationAssessment\VisualizationAssessmentResource;
use Admin\Services\VisualizationAssessmentService;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\DeleteResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class VisualizationAssessmentController extends Controller
{
    public function __construct(public VisualizationAssessmentService $service)
    {
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/applications/visualization-assessment/store",
     *      operationId="createVisualizationAssessment",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Заявки|Визуализация Оценка Коммиссии"},
     *      summary="Создать Оценку к визуализации",
     *      @OA\Parameter(
     *        name="user_application_id",
     *        in="query",
     *        required=true,
     *        description="ID Заявки",
     *        @OA\Schema(
     *            type="integer"
     *       )
     *    ),
     *     @OA\Parameter(
     *         name="visualization_id",
     *         in="query",
     *         required=true,
     *         description="ID Визуализации",
     *         @OA\Schema(
     *             type="integer"
     *        )
     *     ),
     *      @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *        type="object",
     *         required={"status"},
     *          @OA\Property(property="status",description="Статус",type="string",enum={"approved","correction","refused"},example="approved"),
     *          @OA\Property(property="comment",description="комментарий", type="string",example="Потому, что...."),
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
     * @param VisualizationAssessmentSoreRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(VisualizationAssessmentSoreRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $data = $request->validated();
            return new ApiSuccessResponse(
                new  VisualizationAssessmentResource($this->service->store($data)),
                ['message' => 'Оценка успешно добавлен'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при создании оценки',
                $exception
            );
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/applications/visualization-assessment/show",
     *     operationId="visualisationAssessmentShow",
     *     security={{"bearerAuth":{}}},
     *     tags={"Admin|Заявки|Визуализация Оценка Коммиссии"},
     *     summary="Получить(просмотр) оценку",
     *     @OA\Parameter(
     *        name="user_application_id",
     *        in="query",
     *        required=true,
     *        description="ID Заявки",
     *        @OA\Schema(
     *            type="integer"
     *       )
     *    ),
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        required=true,
     *        description="ID Оценки",
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
     * @param VisualizationAssessmentShowRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function show(VisualizationAssessmentShowRequest $request): ApiSuccessResponse
    {
        $appData = $request->validated();
        return new ApiSuccessResponse(
            new  VisualizationAssessmentResource($this->service->show($appData['id'], $appData['user_application_id'])),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/applications/visualization-assessment/list",
     *     operationId="visualisationAllAssessment",
     *     security={{"bearerAuth":{}}},
     *     tags={"Admin|Заявки|Визуализация Оценка Коммиссии"},
     *     summary="Получить(просмотр) Все оценки",
     *     @OA\Parameter(
     *         name="user_application_id",
     *         in="query",
     *         required=true,
     *         description="ID Заявки",
     *         @OA\Schema(
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
     *
     * @param VisualizationAssessmentListRequest $request
     * @return ApiSuccessResponse
     */
    public function list(VisualizationAssessmentListRequest $request): ApiSuccessResponse
    {
        $appData = $request->validated();
        return new ApiSuccessResponse(
            new  VisualizationAssessmentCollection($this->service->list($appData)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }


    /**
     * @OA\Patch(
     *     path="/api/v1/admin/applications/visualization-assessment/update",
     *     operationId="visuallisationAssessmentUpdate",
     *     security={{"bearerAuth":{}}},
     *     tags={"Admin|Заявки|Визуализация Оценка Коммиссии"},
     *     summary="Редактирование оценки",
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        required=true,
     *        description="ID ценки",
     *        @OA\Schema(
     *            type="integer"
     *       )
     *    ),
     *     @OA\Parameter(
     *        name="user_application_id",
     *        in="query",
     *        required=true,
     *        description="ID Заявки",
     *        @OA\Schema(
     *             type="integer"
     *        )
     *     ),
     *      @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *         mediaType="application/vnd.api+json",
     *         @OA\Schema(
     *         type="object",
     *         required={"message"},
     *         @OA\Property(property="status",description="Статус",type="string",enum={"approved","git ","refused"}, example="approved"),
     *         @OA\Property(property="comment",description="комментарий", type="string",example="Потому, что...."),
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
     * @param VisualizationAssessmentUpdateRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(VisualizationAssessmentUpdateRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $appData = $request->validated();
            return new ApiSuccessResponse(
                new  VisualizationAssessmentResource($this->service->update($appData)),
                ['message' => 'Оценка успешно обновлен'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при обновлении оценки',
                $exception
            );
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/admin/applications/visualization-assessment/delete",
     *     operationId="deleteVisuallisationAssessment",
     *     security={{"bearerAuth":{}}},
     *     tags={"Admin|Заявки|Визуализация Оценка Коммиссии"},
     *     summary="Удаление оценки",
     *     @OA\Parameter(
     *         name="user_application_id",
     *         in="query",
     *         required=true,
     *         description="ID Заявки",
     *         @OA\Schema(
     *             type="integer"
     *        )
     *     ),
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="ID Оценки",
     *         @OA\Schema(
     *              type="integer"
     *         )
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
     * @param VisualizationAssessmentShowRequest $request
     * @return DeleteResponse|ApiErrorResponse
     */
    public function delete(VisualizationAssessmentShowRequest $request): DeleteResponse|ApiErrorResponse
    {
        try {
            $appData = $request->validated();
            $message = $this->service->delete($appData['id'], $appData['user_application_id'])
                ? 'Оценка успешно удалена'
                : '';
            return new DeleteResponse($message);
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при удалении Оценки',
                $exception
            );
        }
    }
}
