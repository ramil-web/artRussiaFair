<?php

namespace Admin\Classic\Http\Controllers;

use Admin\Classic\Http\Requests\ClassicAppComment\StoreClassicAppCommentRequest;
use Admin\Classic\Http\Resources\ClassicAppComment\ClassicAppCommentCollection;
use Admin\Classic\Http\Resources\ClassicAppComment\ClassicAppCommentResource;
use Admin\Classic\Services\ClassicAppCommentService;
use Admin\Http\Requests\UserApplication\CommentUserApplicationRequest;
use Admin\Http\Requests\UserApplication\UpdateCommentUserApplicationRequest;
use Admin\Http\Resources\CommentApplication\CommentApplicationCollection;
use Admin\Http\Resources\CommentApplication\CommentApplicationResource;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\DeleteResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class ClassicAppCommentController extends Controller
{
    public function __construct(public ClassicAppCommentService $service)
    {
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/classic/application/{id}/comment",
     *      operationId="createClassicAppManagerComment",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Classic|Заявки|Комментарии менеджера"},
     *      summary="Создать Комментарии к заявке",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="id заявки",
     *       @OA\Schema(
     *            type="integer"
     *       )
     *    ),
     *      @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *      type="object",
     *     required={"message","locate"},
     *          @OA\Property(property="message",description="комментарий", type="string",example="Потому, что...."),
     *          @OA\Property(property="locate",description="Язык", type="string", example="ru"),
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
     */
    public function store(int $id, StoreClassicAppCommentRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $data = $request->all();
            $data['classic_user_application_id'] = $id;
            return new ApiSuccessResponse(
                new  ClassicAppCommentResource($this->service->create($data)),
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
     *    path="/api/v1/admin/classic/application/{id}/comment",
     *    operationId="getClassicAppAllManagerComment",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Classic|Заявки|Комментарии менеджера"},
     *    summary="Получить(просмотр) Все коммментарии",
     *    @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       description="id заявки",
     *       @OA\Schema(
     *            type="integer"
     *       )
     *    ),
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
     * @param int $id
     * @param Request $request
     * @return ApiSuccessResponse
     */
    public function list(int $id, Request $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            new  ClassicAppCommentCollection($this->service->list($id, $request)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/classic/application/{id}/comment/{comment_id}",
     *      operationId="getClassicAppManagerComment",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Classic|Заявки|Комментарии менеджера"},
     *      summary="Получить(просмотр) коммментария",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="id заявки",
     *       @OA\Schema(
     *            type="integer"
     *       )
     *    ),
     *     @OA\Parameter(
     *          name="comment_id",
     *          in="path",
     *          required=true,
     *      description="id комментария",
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
     *
     * @throws CustomException
     */
    public function show(int $id, int $comment_id, Request $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            new  CommentApplicationResource($this->service->show($comment_id, $id)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/admin/classic/application/{id}/comment/{comment_id}",
     *      operationId="editClassicAppManagerComment",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Classic|Заявки|Комментарии менеджера"},
     *      summary="Редактирование коммментария",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     description="id заявки",
     *       @OA\Schema(
     *            type="integer"
     *       )
     *    ),
     *     @OA\Parameter(
     *          name="comment_id",
     *          in="path",
     *          required=true,
     *      description="id комментария",
     *        @OA\Schema(
     *             type="integer"
     *        )
     *     ),
     *      @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *      type="object",
     *     required={"message","locate"},
     *          @OA\Property(property="message",description="комментарий", type="string",example="Потому, что...."),
     *          @OA\Property(property="locate",description="Язык", type="string", example="ru"),
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
     */
    public function update(
        int                                 $id,
        int                                 $comment_id,
        UpdateCommentUserApplicationRequest $request
    ): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $data = $request->all();
            return new ApiSuccessResponse(
                new  CommentApplicationResource($this->service->update($comment_id, $data)),
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
     *      path="/api/v1/admin/classic/application/{id}/comment/{comment_id}",
     *      operationId="deleteClassicAppManagerComment",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Classic|Заявки|Комментарии менеджера"},
     *      summary="Удаление комментария",
     *     @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="id заявки",
     *        @OA\Schema(
     *            type="integer"
     *       )
     *    ),
     *     @OA\Parameter(
     *          name="comment_id",
     *          in="path",
     *          required=true,
     *      description="id комментария",
     *        @OA\Schema(
     *             type="integer"
     *        )
     *     ),
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      @OA\Schema(
     *         @OA\Property(property="data", type="bolean", example=true),
     *         @OA\Property(property="metadata", type="object",
     *             @OA\Property(property="message", type="string", example="Комментарий успешно удалён.")
     *          ),
     *        )
     *      ),
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
     * @param int $id
     * @param int $commentId
     * @return DeleteResponse|ApiErrorResponse
     */
    public function destroy(int $id, int $commentId): DeleteResponse|ApiErrorResponse
    {
        try {
            $message = $this->service->delete($id, $commentId) ? 'Комментарий успешно удалён' : '';
            return new DeleteResponse($message);
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при удалении комментария',
                $exception
            );
        }
    }
}
