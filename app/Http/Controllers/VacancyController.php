<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vacancy\ShowVacancyRequest;
use App\Http\Resources\Vacancy\VacancyCollection;
use App\Http\Resources\Vacancy\VacancyResource;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\VacancyService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class VacancyController extends Controller
{
    public function __construct(public VacancyService $service)
    {
    }

    /**
     * @OA\Get(
     *    path="/api/v1/vacancy/show",
     *    operationId="AppShowVacancy",
     *    security={{"bearerAuth":{}}},
     *    tags={"App|Вакансии"},
     *    summary="Просмотр вакансии",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
     *       description="ID вакансии",
     *        @OA\Schema(
     *           type="integer",
     *           example="1",
     *            )
     *       ),
     *      @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            @OA\Property(
     *              property="data",
     *              description="ID вакансии",
     *              type="object",
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="vacancy", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="name", description="Название вакансии", type="object",
     *                     @OA\Property(property="ru", example="Супер Вакансия"),
     *                     @OA\Property(property="en", example="Super vacancy")
     *                 ),
     *                 @OA\Property(property="description", description="Описание", type="object",
     *                    @OA\Property(property="ru", example="Описание вакансии"),
     *                    @OA\Property(property="en", example="Vacancy description")
     *                 ),
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="id", type="integer", example=1),
     *               ),
     *              @OA\Property(property="links", type="object",
     *                  @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/vacancy/show?id=4"),
     *               ),
     *              @OA\Property(property="relationships", type="array",
     *                 @OA\Items()
     *               ),
     *             ),
     *                 @OA\Property(property="metadata",type="object",
     *                    @OA\Property(property="message", example="Ok."),
     *                  ),
     *               ),
     *              ),
     *            ),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=400,description="Bad Request"),
     *      @OA\Response(response=404,description="not found"),
     *            @OA\Response(response=403,description="Forbidden",
     *           @OA\JsonContent(
     *               @OA\Property(property="errors", type="array",
     *               @OA\Items(
     *                   @OA\Property(property="status", example="403"),
     *                   @OA\Property(property="detail", example="User does not have the right roles.")
     *               ),
     *            ),
     *          ),
     *       ),
     *      @OA\Response(response=500,description="Server error")
     * )
     * @param ShowVacancyRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function show(ShowVacancyRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $dataApp = $request->validated();
            return new ApiSuccessResponse(
                new VacancyResource($this->service->show($dataApp['id'])),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при получени вакансии',
                $exception
            );
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/vacancy/list",
     *      operationId="AppListVacancy",
     *      security={{"bearerAuth":{}}},
     *      tags={"App|Вакансии"},
     *      summary="Получение списка вакансий",
     *      @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *               property="data",
     *               description="ID вакансии",
     *               type="object",
     *               @OA\Property(property="id", example="1", type="integer"),
     *               @OA\Property(property="type", example="vacancy", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", description="Название вакансии", type="object",
     *                      @OA\Property(property="ru", example="Супер Вакансия"),
     *                      @OA\Property(property="en", example="Super vacancy")
     *                  ),
     *                  @OA\Property(property="description", description="Описание", type="object",
     *                     @OA\Property(property="ru", example="Описание вакансии"),
     *                     @OA\Property(property="en", example="Vacancy description")
     *                  ),
     *                  @OA\Property(property="status", type="boolean", example=true),
     *                  @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                  @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/vacancy/show?id=4"),
     *                ),
     *               @OA\Property(property="relationships", type="array",
     *                  @OA\Items()
     *                ),
     *              ),
     *                  @OA\Property(property="metadata",type="object",
     *                     @OA\Property(property="message", example="Вакаснсия успешно обнавлена"),
     *                   ),
     *                ),
     *               ),
     *             ),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=400,description="Bad Request"),
     *      @OA\Response(response=404,description="not found"),
     *            @OA\Response(response=403,description="Forbidden",
     *           @OA\JsonContent(
     *               @OA\Property(property="errors", type="array",
     *               @OA\Items(
     *                   @OA\Property(property="status", example="403"),
     *                   @OA\Property(property="detail", example="User does not have the right roles.")
     *               ),
     *            ),
     *          ),
     *       ),
     *      @OA\Response(response=500,description="Server error")
     * )
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function list(): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            return new ApiSuccessResponse(
                new VacancyCollection($this->service->list()),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при получение списка вакансии',
                $exception
            );
        }
    }

}
