<?php

namespace Lk\Classic\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Http\Request;
use Lk\Classic\Services\ClassicAppCommentService;
use Lk\Http\Resources\CommentApplication\CommentApplicationCollection;
use Lk\Http\Resources\CommentApplication\CommentApplicationResource;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ClassicAppCommentController extends Controller
{

    public function __construct( public ClassicAppCommentService $service)
    {
    }


    /**
     * @OA\Get(
     *      path="/api/v1/lk/classic/application/{id}/comment/{comment_id}",
     *      operationId="getClassicAppComment",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Classic|Заявки|Комментарии менеджера"},
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
            new  CommentApplicationResource($this->service->show($comment_id, $request)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *      path="/api/v1/lk/classic/application/{id}/comment",
     *      operationId="getClassicAppAllComment",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Classic|Заявки|Комментарии менеджера"},
     *      summary="Получить(просмотр) Все коммментарии",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="id заявки",
     *       @OA\Schema(
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
     */
    public function list(int $id, Request $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            new  CommentApplicationCollection($this->service->list($id, $request)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }
}
