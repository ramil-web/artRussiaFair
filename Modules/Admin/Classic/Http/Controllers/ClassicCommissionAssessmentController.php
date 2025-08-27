<?php

namespace Admin\Classic\Http\Controllers;

use Admin\Classic\Http\Requests\ClassicCommissionAssessment\ClassicAssessmentApplicationRequest;
use Admin\Classic\Http\Requests\UpdateClassicAssessmentAppRequest;
use Admin\Classic\Http\Resources\ClassicAssessmentApplication\ClassicAssessmentApplicationCollection;
use Admin\Classic\Http\Resources\ClassicAssessmentApplication\ClassicAssessmentApplicationResource;
use Admin\Classic\Services\ClassicCommissionAssessmentService;
use Admin\Http\Requests\UserApplication\UpdateAssessmentApplicationRequest;
use Admin\Http\Resources\AssessmentApplication\AssessmentApplicationCollection;
use Admin\Http\Resources\AssessmentApplication\AssessmentApplicationResource;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\DeleteResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class ClassicCommissionAssessmentController extends Controller
{
    public function __construct(public ClassicCommissionAssessmentService $service)
    {
    }

    /**
     * @OA\Post(
     *    path="/api/v1/admin/classic/application/{id}/assessment",
     *    operationId="createClassicAppAssesment",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Classic|Заявки|Оценка Комисии"},
     *    summary="Создать Оценку к заявке",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="id заявки",
     *        @OA\Schema(
     *            type="integer"
     *       )
     *    ),
     *    @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *           type="object",
     *           required={"message"},
     *           @OA\Property(property="status",description="Статус",type="string",enum={"approved","correction","refused"},example="approved"),
     *           @OA\Property(property="comment",description="комментарий", type="string",example="Потому, что...."),
     *       ),
     *     ),
     *     ),
     *
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *      description="Unauthenticated"
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
     *
     * @param int $id
     * @param ClassicAssessmentApplicationRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(int $id, ClassicAssessmentApplicationRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $data = $request->all();
            $data['classic_user_application_id'] = $id;
            return new ApiSuccessResponse(
                new  ClassicAssessmentApplicationResource($this->service->create($data)),
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
     *    path="/api/v1/admin/classic/application/{id}/assessment/{assessment_id}",
     *    operationId="getClassicAppAssesment",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Classic|Заявки|Оценка Комисии"},
     *    summary="Получить(просмотр) оценку",
     *    @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       description="id заявки",
     *       @OA\Schema(
     *            type="integer"
     *       )
     *    ),
     *    @OA\Parameter(
     *        name="assessment_id",
     *        in="path",
     *        required=true,
     *        description="id оценки",
     *        @OA\Schema(
     *             type="integer"
     *        )
     *     ),
     *    @OA\Response(
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
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *   @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     * @param int $id
     * @param int $assessment_id
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function show(int $id, int $assessment_id): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            new  AssessmentApplicationResource($this->service->show($id, $assessment_id)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/classic/application/{id}/assessment/list",
     *    operationId="getAppAllAssessmentData",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Classic|Заявки|Оценка Комисии"},
     *    summary="Получить(просмотр) Все оценки",
     *    @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       description="id заявки",
     *       @OA\Schema(
     *            type="integer"
     *       )
     *    ),
     *    @OA\Response(
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
            new  ClassicAssessmentApplicationCollection($this->service->list($id, $request)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Patch(
     *    path="/api/v1/admin/classic/application/{id}/assessment/{assessment_id}",
     *    operationId="editClassicAppAssesment",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Classic|Заявки|Оценка Комисии"},
     *    summary="Редактирование оценки",
     *    @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       description="id заявки",
     *       @OA\Schema(
     *          type="integer"
     *       )
     *    ),
     *    @OA\Parameter(
     *       name="assessment_id",
     *       in="path",
     *       required=true,
     *       description="id оценки",
     *       @OA\Schema(
     *          type="integer"
     *        )
     *     ),
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *          type="object",
     *          required={"message"},
     *          @OA\Property(property="status",description="Статус",type="string",enum={"approved","git ","refused"}, example="approved"),
     *          @OA\Property(property="comment",description="комментарий", type="string",example="Потому, что...."),
     *     ),
     *   ),
     * ),
     *    @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\MediaType(
     *         mediaType="application/vnd.api+json",
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
     *   )
     * @param int $id
     * @param int $assessment_id
     * @param UpdateClassicAssessmentAppRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(
        int                               $id,
        int                               $assessment_id,
        UpdateClassicAssessmentAppRequest $request
    ): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $data = $request->validated();
            return new ApiSuccessResponse(
                new  ClassicAssessmentApplicationResource($this->service->update($id, $assessment_id, $data)),
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
     *    path="/api/v1/admin/classic/application/{id}/assessment/{assessment_id}",
     *    operationId="deleteClassicAppAssesment",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Classic|Заявки|Оценка Комисии"},
     *    summary="Удаление оценки",
     *    @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       description="id заявки",
     *       @OA\Schema(
     *          type="integer"
     *      )
     *  ),
     *   @OA\Parameter(
     *      name="assessment_id",
     *      in="path",
     *      required=true,
     *      description="id оценки",
     *      @OA\Schema(
     *         type="integer"
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\MediaType(
     *         mediaType="application/vnd.api+json",
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
     * @param $assessment_id
     * @return DeleteResponse|ApiErrorResponse
     */
    public function destroy(int $id, $assessment_id): DeleteResponse|ApiErrorResponse
    {
        try {
            $message = $this->service->delete($id, $assessment_id) ? 'Оценка успешно удалена' : '';
            return new DeleteResponse($message);
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при удалении Оценки',
                $exception
            );
        }
    }
}
