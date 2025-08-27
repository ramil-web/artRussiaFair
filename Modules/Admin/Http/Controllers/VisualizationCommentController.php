<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\VisualisationComment\ListVisualizationCommentRequest;
use Admin\Http\Requests\VisualisationComment\ShowVisualizationCommentRequest;
use Admin\Http\Requests\VisualisationComment\StoreVisualisationCommentRequest;
use Admin\Http\Requests\VisualisationComment\UpdateVisualizationCommentRequest;
use Admin\Http\Resources\CommentApplication\CommentApplicationResource;
use Admin\Http\Resources\VisualizationComment\VisualizationCommentResource;
use Admin\Services\VisualizationCommentService;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\DeleteResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class VisualizationCommentController extends Controller
{
    public function __construct(public VisualizationCommentService $service)
    {
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/applications/visualization-comment/store",
     *      operationId="createVisualizationComment",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Заявки|Визуализация Комментарии менеджера"},
     *      summary="Создать Комментарии к визуализации",
     *      @OA\Parameter(
     *          name="visualization_id",
     *          in="query",
     *          required=true,
     *          description="ID Визуализации",
     *          @OA\Schema(
     *              type="integer"
     *         )
     *      ),
     *     @OA\Parameter(
     *        name="user_application_id",
     *        in="query",
     *        required=true,
     *        description="ID Заявки",
     *        @OA\Schema(
     *            type="integer"
     *       )
     *    ),
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *       mediaType="application/vnd.api+json",
     *       @OA\Schema(
     *          type="object",
     *          required={"message","locate"},
     *          @OA\Property(property="message",description="комментарий", type="string",example="Потому, что...."),
     *          @OA\Property(property="locate",description="Язык", type="string", example="ru"),
     *         ),
     *       ),
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
     *
     */
    public function store(StoreVisualisationCommentRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $appData = $request->validated();
            return new ApiSuccessResponse(
                new  VisualizationCommentResource($this->service->create($appData)),
                ['message' => 'Комментарий успешно добавлен'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при создании комментария',
                $exception
            );
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/applications/visualization-comment/show",
     *      operationId="showVisualizationComment",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Заявки|Визуализация Комментарии менеджера"},
     *      summary="Получает Комментарии к визуализации",
     *      @OA\Parameter(
     *         name="user_application_id",
     *         in="query",
     *         required=true,
     *         description="ID Заявки",
     *         @OA\Schema(
     *             type="integer"
     *        )
     *     ),
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        required=true,
     *        description="ID комментария",
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
     * @param ShowVisualizationCommentRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function show(ShowVisualizationCommentRequest $request): ApiSuccessResponse
    {
        $appData = $request->validated();
        return new ApiSuccessResponse(
            new  VisualizationCommentResource($this->service->show($appData['id'], $appData['user_application_id'])),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/applications/visualization-comment/list",
     *      operationId="listVisualizationComment",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Заявки|Визуализация Комментарии менеджера"},
     *      summary="Получает все коммментарии",
     *      @OA\Parameter(
     *          name="user_application_id",
     *          in="query",
     *          required=true,
     *          description="ID Заявки",
     *          @OA\Schema(
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
     *
     */
    public function list(ListVisualizationCommentRequest $request): ApiSuccessResponse
    {
        $appData = $request->validated();
        return new ApiSuccessResponse(
            $this->service->list($appData),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/admin/applications/visualization-comment/update",
     *      operationId="updateVisualizationComment",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Заявки|Визуализация Комментарии менеджера"},
     *      summary="Редактирование Комментарии к визуализации",
     *      @OA\Parameter(
     *         name="user_application_id",
     *         in="query",
     *         required=true,
     *         description="ID Заявки",
     *         @OA\Schema(
     *              type="integer"
     *         )
     *      ),
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        required=true,
     *        description="ID Комментарий",
     *        @OA\Schema(
     *            type="integer"
     *       )
     *    ),
     *      @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *          type="object",
     *          required={"message"},
     *          @OA\Property(property="message",description="комментарий", type="string",example="Потому, что...."),
     *          @OA\Property(property="locate",description="Язык", type="string", example="ru"),
     *         ),
     *        ),
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
     * @param UpdateVisualizationCommentRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(UpdateVisualizationCommentRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $appData = $request->validated();
            return new ApiSuccessResponse(
                new  CommentApplicationResource($this->service->update($appData)),
                ['message' => 'Комментарий успешно обновлен'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при обновлении комментария',
                $exception
            );
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/admin/applications/visualization-comment/delete",
     *      operationId="deleteVisualizationComment",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Заявки|Визуализация Комментарии менеджера"},
     *      summary="Редактирование Комментарии к визуализации",
     *      @OA\Parameter(
     *          name="user_application_id",
     *          in="query",
     *          required=true,
     *          description="ID Заявки",
     *          @OA\Schema(
     *              type="integer"
     *         )
     *      ),
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        required=true,
     *        description="ID Комментарий",
     *        @OA\Schema(
     *            type="integer"
     *       )
     *    ),
     *      @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *          type="object",
     *          required={"message"},
     *          @OA\Property(property="message",description="комментарий", type="string",example="Потому, что...."),
     *          @OA\Property(property="locate",description="Язык", type="string", example="ru"),
     *         ),
     *        ),
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
     * @param ShowVisualizationCommentRequest $request
     * @return DeleteResponse|ApiErrorResponse
     */
    public function delete(ShowVisualizationCommentRequest $request): DeleteResponse|ApiErrorResponse
    {
        try {
            $appData = $request->validated();
            $message = $this->service->delete($appData['id'], $appData['user_application_id'])
                ? 'Комментарий успешно удалён' :
                '';
            return new DeleteResponse($message);
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при удалении комментария',
                $exception
            );
        }
    }
}
