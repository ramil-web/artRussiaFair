<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\Vacancy\CreateVacancyRequest;
use Admin\Http\Requests\Vacancy\ShowVacancyRequest;
use Admin\Http\Requests\Vacancy\UpdateVacancyRequest;
use Admin\Http\Resources\Vacancy\VacancyCollection;
use Admin\Http\Resources\Vacancy\VacancyResource;
use Admin\Services\VacancyService;
use App\Exceptions\CustomException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class VacancyController extends Controller
{
    public function __construct(public VacancyService $service)
    {
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/vacancy/store",
     *      operationId="AdminCreateVacancy",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Вакансии"},
     *      summary="Добавление вакансии",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            required={"name", "description"},
     *            @OA\Property(property="name", description="Название вакансии", type="object",
     *               @OA\Property(property="ru", example="Менеджер по подбору персонала"),
     *               @OA\Property(property="en", example="Recruitment Manager")
     *            ),
     *            @OA\Property(property="description", description="Описание вакансии", type="object",
     *               @OA\Property(property="ru", example="Текст"),
     *               @OA\Property(property="en", example="text")
     *             ),
     *            @OA\Property(property="place", description="Место", type="object",
     *                @OA\Property(property="ru", example="Текст"),
     *                @OA\Property(property="en", example="text")
     *              ),
     *            @OA\Property(property="status", description="Статус", type="boolean", example=true),
     *            ),
     *          ),
     *      ),
     *      @OA\Response(
     *       response=200,
     *       description="Success",
     *       @OA\MediaType(
     *         mediaType="application/vnd.api+json",
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="data",
     *             description="ID вакансии",
     *             type="object",
     *             @OA\Property(property="id", example="1", type="integer"),
     *             @OA\Property(property="type", example="vacancy", type="string"),
     *                @OA\Property(property="attributes", type="object",
     *                @OA\Property(property="id", type="integer", example=1),
     *                @OA\Property(property="name", description="Название вакансии", type="object",
     *                    @OA\Property(property="ru", example="Супер Вакансия"),
     *                    @OA\Property(property="en", example="Super vacancy")
     *                ),
     *                @OA\Property(property="description", description="Описание", type="object",
     *                   @OA\Property(property="ru", example="Описание вакансии"),
     *                   @OA\Property(property="en", example="Vacancy description")
     *                ),
     *                @OA\Property(property="place", description="Место", type="object",
     *                    @OA\Property(property="ru", example="Галерея"),
     *                    @OA\Property(property="en", example="Gallery")
     *                 ),
     *                @OA\Property(property="status", type="boolean", example=true),
     *                @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *              ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/vacancy/show?id=4"),
     *              ),
     *             @OA\Property(property="relationships", type="array",
     *                @OA\Items()
     *              ),
     *            ),
     *                @OA\Property(property="metadata",type="object",
     *                   @OA\Property(property="message", example="Вакаснсия успешно добавлена"),
     *                 ),
     *              ),
     *             ),
     *           ),
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
     * @param CreateVacancyRequest $request
     * @return ApiErrorResponse|ApiSuccessResponse
     */
    public function store(CreateVacancyRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $dataApp = $request->validated();
            return new ApiSuccessResponse(
                new VacancyResource($this->service->create($dataApp)),
                ['message' => 'Вакансия успешно добавлена'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при добавление вакансии',
                $exception
            );
        }
    }
    /**
     * @OA\Get(
     *    path="/api/v1/admin/vacancy/show",
     *    operationId="AdminShowVacancy",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Вакансии"},
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
     *                @OA\Property(property="place", description="Место", type="object",
     *                     @OA\Property(property="ru", example="Галерея"),
     *                     @OA\Property(property="en", example="Gallery")
     *                  ),
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="id", type="integer", example=1),
     *               ),
     *              @OA\Property(property="links", type="object",
     *                  @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/vacancy/show?id=4"),
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
     *      path="/api/v1/admin/vacancy/list",
     *      operationId="AdminListVacancy",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Вакансии"},
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
     *                @OA\Property(property="place", description="Место", type="object",
     *                     @OA\Property(property="ru", example="Галерея"),
     *                     @OA\Property(property="en", example="Gallery")
     *                  ),
     *                  @OA\Property(property="status", type="boolean", example=true),
     *                  @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                  @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/vacancy/show?id=4"),
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

    /**
     * @OA\Patch(
     *    path="/api/v1/admin/vacancy/update",
     *    operationId="AdminUpdateVacancy",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Вакансии"},
     *    summary="Редактирование вакансии",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
     *       description="ID вакансии",
     *       @OA\Schema(
     *          type="integer",
     *          example="1",
     *          )
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            @OA\Property(property="name", description="Название вакансии", type="object",
     *               @OA\Property(property="ru", example="Менеджер по подбору персонала"),
     *               @OA\Property(property="en", example="Recruitment Manager")
     *            ),
     *            @OA\Property(property="description", description="Описание вакансии", type="object",
     *               @OA\Property(property="ru", example="Текст"),
     *               @OA\Property(property="en", example="text")
     *             ),
     *            @OA\Property(property="place", description="Место", type="object",
     *                 @OA\Property(property="ru", example="Текст"),
     *                 @OA\Property(property="en", example="text")
     *               ),
     *            @OA\Property(property="status", description="Статус", type="boolean", example=true),
     *            ),
     *          ),
     *      ),
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
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", description="Название вакансии", type="object",
     *                     @OA\Property(property="ru", example="Супер Вакансия"),
     *                     @OA\Property(property="en", example="Super vacancy")
     *                 ),
     *                 @OA\Property(property="description", description="Описание", type="object",
     *                    @OA\Property(property="ru", example="Описание вакансии"),
     *                    @OA\Property(property="en", example="Vacancy description")
     *                 ),
     *                @OA\Property(property="place", description="Место", type="object",
     *                     @OA\Property(property="ru", example="Галерея"),
     *                     @OA\Property(property="en", example="Gallery")
     *                  ),
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *               ),
     *              @OA\Property(property="links", type="object",
     *                  @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/vacancy/show?id=4"),
     *               ),
     *              @OA\Property(property="relationships", type="array",
     *                 @OA\Items()
     *               ),
     *             ),
     *                 @OA\Property(property="metadata",type="object",
     *                    @OA\Property(property="message", example="Вакаснсия успешно обнавлена"),
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
     * @param UpdateVacancyRequest $request
     * @return ApiErrorResponse|ApiSuccessResponse
     */
    public function update(UpdateVacancyRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $dataApp = $request->validated();
            return new ApiSuccessResponse(
                new VacancyResource($this->service->update($dataApp)),
                ['message' => 'Вакансия успешно обновилась'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при обновлени вакансии',
                $exception
            );
        }
    }

    /**
     * @OA\Delete(
     *    path="/api/v1/admin/vacancy/destroy",
     *    operationId="AdminDestroyVacancy",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Вакансии"},
     *    summary="Удаление вакансии",
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
     *       response=200,
     *       description="Success",
     *       @OA\MediaType(
     *         mediaType="application/vnd.api+json",
     *         @OA\Schema(
     *           type="object",
     *           @OA\Property(
     *             property="data",
     *             description="ID программа",
     *             type="object",
     *             @OA\Property(property="id", example="1", type="integer"),
     *             @OA\Property(property="type", example="program", type="string"),
     *                @OA\Property(property="attributes", type="object",
     *                   @OA\Property(property="start_time", description="Время, начало программы", type="string", example="15:30:00"),
     *                   @OA\Property(property="end_time", description="Время, конец программы", type="string", example="14:30:00"),
     *                   @OA\Property(property="date", type="sting", example="2024-11-03"),
     *             @OA\Property(property="name", description="Название программы", type="object",
     *                @OA\Property(property="ru", example="Российский арт-рынок"),
     *                @OA\Property(property="en", example="Russian art program")
     *             ),
     *             @OA\Property(property="moderator_name", description="ФИЩ модератора", type="object",
     *                @OA\Property(property="ru", example="Василий Пупкин"),
     *                @OA\Property(property="en", example="John")
     *              ),
     *             @OA\Property(property="moderator_description", description="ФИЩ модератора", type="object",
     *                 @OA\Property(property="ru", example="Василий Пупкин крутой модератор"),
     *                 @OA\Property(property="en", example="John moderator")
     *               ),
     *                   @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                   @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                   @OA\Property(property="event", description="события", type="object"),
     *                   @OA\Property(property="speaker", type="array",
     *                      @OA\Items(type="object",
     *                      ),
     *                 ),
     *             ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12"),
     *              ),
     *             @OA\Property(property="relationships", type="object",
     *                @OA\Property(property="events", type="object",
     *                  @OA\Property(property="data",type="object"),
     *                  @OA\Property(property="links",type="object",
     *                     @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12/relationships/event"),
     *                     @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/program/12/event" ),
     *                 ),
     *                ),
     *                @OA\Property(property="speakers", type="object",
     *                     @OA\Property(property="data",type="object"),
     *                    @OA\Property(property="links",type="object",
     *                    @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12/relationships/speakers"),
     *                    @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/program/12/speakers" ),
     *                    ),
     *                   ),
     *              ),
     *            ),
     *                @OA\Property(property="metadata",type="object",
     *                   @OA\Property(property="message", example="Программа успешно добавлена"),
     *                 ),
     *              ),
     *             ),
     *           ),
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
     * @return ApiSuccessResponse|ResourceNotFoundException
     * @throws CustomException
     */
    public function destroy(ShowVacancyRequest $request): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            $appData = $request->validated();
            return new ApiSuccessResponse(
                $this->service->delete($appData['id']),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }
}
