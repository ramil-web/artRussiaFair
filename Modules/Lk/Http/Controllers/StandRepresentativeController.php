<?php

namespace Lk\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Lk\Http\Requests\MyTeam\StandRepresentative\DeleteStandRepresentativeRequest;
use Lk\Http\Requests\MyTeam\StandRepresentative\ShowStandRepresentativeRequest;
use Lk\Http\Requests\MyTeam\StandRepresentative\StoreStandRepresentativeRequest;
use Lk\Http\Requests\MyTeam\StandRepresentative\UpdateStandRepresentativeRequest;
use Lk\Http\Resources\MyTeam\StandRepresentative\StandRepresentativeResource;
use Lk\Services\StandRepresentativeService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class StandRepresentativeController extends Controller
{
    public function __construct(protected StandRepresentativeService $service)
    {
    }

    /**
     * @OA\Post(
     *    path="/api/v1/lk/my-team/stand-representative/store",
     *    operationId="Lk.myTeamStandRepresentativeStore",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Моя команда|Представители стенда"},
     *    summary="Добавление представителя стенда в команду",
     *    @OA\Parameter(
     *       name="user_application_id",
     *       in="query",
     *       required=true,
     *        description="ID заявки",
     *       @OA\Schema(
     *            type="integer",
     *            example=1
     *       )
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *             type="object",
     *             required={"square","check_in","exit"},
     *             @OA\Property(property="full_name",description="ФИО",type="string",example="Соловев Иван Василевич"),
     *             @OA\Property(property="passport",description="Серия номер паспорта",type="string",example="23232342342"),
     *              ),
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
     *              description="ID Команды",
     *              type="object",
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="my-team.stand-representative", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="full_name",description="ФИО",type="string",example="Иванов Иван Василевич"),
     *                 @OA\Property(property="passport",description="Серия рнмер паспорта",type="string",example="43423535"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *               ),
     *              @OA\Property(property="links", type="object",
     *                  @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team/stand-representative/show?id=1"),
     *               ),
     *              @OA\Property(property="relationships", type="object",
     *                     @OA\Property(property="user_application", type="object",
     *                     @OA\Property(property="data",type="object",example=null),
     *                     @OA\Property(property="links", type="object",
     *                         @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team.stand-representative/1/relationships/user_application"),
     *                         @OA\Property(property="related",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team.stand-representative/1/user_application"),
     *                       ),
     *                     ),
     *                   ),
     *               ),
     *                 @OA\Property(property="metadata",type="object",
     *                    @OA\Property(property="message", example="Застройщик успешно добавлен"),
     *                  ),
     *               ),
     *              ),
     *            ),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=400,description="Bad Request"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=403,description="Forbidden",
     *          @OA\JsonContent(
     *              @OA\Property(property="errors", type="array",
     *              @OA\Items(
     *                  @OA\Property(property="status", example="403"),
     *                  @OA\Property(property="detail", example="User does not have the right roles.")
     *              ),
     *           ),
     *         ),
     *      ),
     * )
     * @param StoreStandRepresentativeRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(StoreStandRepresentativeRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $dataApp = $request->validated();

            $documents = $this->service->store($dataApp);
            return new ApiSuccessResponse(
                new StandRepresentativeResource($documents),
                ['message' => 'Застройщик успешно добавлен'],
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при добавлении представителя стенда', $e);
        }
    }

    /**
     * @OA\Get(
     *    path="/api/v1/lk/my-team/stand-representative/show",
     *    operationId="Lk.myTeamStandRepresentativeShow",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Моя команда|Представители стенда"},
     *    summary="Получает представителя стенда",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
     *        description="ID представителя стенда",
     *       @OA\Schema(
     *            type="integer",
     *            example=1
     *       )
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *               property="data",
     *               description="ID Команды",
     *               type="object",
     *               @OA\Property(property="id", example="1", type="integer"),
     *               @OA\Property(property="type", example="my-team.stand-representative", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *                  @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                  @OA\Property(property="full_name",description="ФИО",type="string",example="Иванов Иван Василевич"),
     *                  @OA\Property(property="passport",description="Серия рнмер паспорта",type="string",example="43423535"),
     *                  @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                  @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team/stand-representative/show?id=1"),
     *                ),
     *               @OA\Property(property="relationships", type="object",
     *                      @OA\Property(property="user_application", type="object",
     *                      @OA\Property(property="data",type="object",example=null),
     *                      @OA\Property(property="links", type="object",
     *                          @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team.stand-representative/1/relationships/user_application"),
     *                          @OA\Property(property="related",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team.stand-representative/1/user_application"),
     *                        ),
     *                      ),
     *                    ),
     *                ),
     *                  @OA\Property(property="metadata",type="object",
     *                     @OA\Property(property="message", example="Застройщик успешно добавлен"),
     *                   ),
     *                ),
     *               ),
     *             ),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=400,description="Bad Request"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=403,description="Forbidden",
     *          @OA\JsonContent(
     *              @OA\Property(property="errors", type="array",
     *              @OA\Items(
     *                  @OA\Property(property="status", example="403"),
     *                  @OA\Property(property="detail", example="User does not have the right roles.")
     *              ),
     *           ),
     *         ),
     *      ),
     * )
     * @param ShowStandRepresentativeRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function show(ShowStandRepresentativeRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $dataApp = $request->validated();

            $documents = $this->service->show($dataApp['id']);
            return new ApiSuccessResponse(
                new StandRepresentativeResource($documents),
                ['message' => 'Ок'],
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при получении представителя стенда', $e);
        }
    }

    /**
     * @OA\Patch(
     *    path="/api/v1/lk/my-team/stand-representative/update",
     *    operationId="Lk.myTeamStandRepresentativeUpdate",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Моя команда|Представители стенда"},
     *    summary="Редактирование представителя стенда",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
     *        description="ID представителя стенда",
     *       @OA\Schema(
     *            type="integer",
     *            example=1
     *       )
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *             type="object",
     *             @OA\Property(property="full_name",description="ФИО",type="string",example="Соловев Иван Василевич"),
     *             @OA\Property(property="passport",description="Серия номер паспорта",type="string",example="23232342342"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *               property="data",
     *               description="ID Команды",
     *               type="object",
     *               @OA\Property(property="id", example="1", type="integer"),
     *               @OA\Property(property="type", example="my-team.stand-representative", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *                  @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                  @OA\Property(property="full_name",description="ФИО",type="string",example="Иванов Иван Василевич"),
     *                  @OA\Property(property="passport",description="Серия рнмер паспорта",type="string",example="43423535"),
     *                  @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                  @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team/stand-representative/show?id=1"),
     *                ),
     *               @OA\Property(property="relationships", type="object",
     *                      @OA\Property(property="user_application", type="object",
     *                      @OA\Property(property="data",type="object",example=null),
     *                      @OA\Property(property="links", type="object",
     *                          @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team.stand-representative/1/relationships/user_application"),
     *                          @OA\Property(property="related",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team.stand-representative/1/user_application"),
     *                        ),
     *                      ),
     *                    ),
     *                ),
     *                  @OA\Property(property="metadata",type="object",
     *                     @OA\Property(property="message", example="Застройщик успешно добавлен"),
     *                   ),
     *                ),
     *               ),
     *             ),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=400,description="Bad Request"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=403,description="Forbidden",
     *          @OA\JsonContent(
     *              @OA\Property(property="errors", type="array",
     *              @OA\Items(
     *                  @OA\Property(property="status", example="403"),
     *                  @OA\Property(property="detail", example="User does not have the right roles.")
     *              ),
     *           ),
     *         ),
     *      ),
     * )
     * @param UpdateStandRepresentativeRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(UpdateStandRepresentativeRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $dataApp = $request->validated();
            return new ApiSuccessResponse(
                new StandRepresentativeResource($this->service->update($dataApp)),
                ['message' => 'Данные представителя стенда успешно обновлены'],
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при редактирование представителя стенда', $e);
        }
    }

    /**
     * @OA\Delete(
     *    path="/api/v1/lk/my-team/stand-representative/delete",
     *    operationId="Lk.myTeamStandRepresentativeDelete",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Моя команда|Представители стенда"},
     *    summary="Удаление представителя стенда",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
     *        description="ID представителя стенда",
     *       @OA\Schema(
     *            type="integer",
     *            example=1
     *       )
     *      ),
     *      @OA\Response(
     *           response=200,
     *           description="Success",
     *           @OA\MediaType(
     *             mediaType="application/vnd.api+json",
     *             @OA\Schema(
     *                 @OA\Property(property="data", type="boolean", example="true"),
     *                 @OA\Property(property="metadata",type="object",
     *                    @OA\Property(property="message", example="Ok"),
     *                 ),
     *               ),
     *            ),
     *        ),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=400,description="Bad Request"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=403,description="Forbidden",
     *          @OA\JsonContent(
     *              @OA\Property(property="errors", type="array",
     *              @OA\Items(
     *                  @OA\Property(property="status", example="403"),
     *                  @OA\Property(property="detail", example="User does not have the right roles.")
     *              ),
     *           ),
     *         ),
     *      ),
     * )
     * @param DeleteStandRepresentativeRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function delete(DeleteStandRepresentativeRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $dataApp = $request->validated();
            return new ApiSuccessResponse(
                $this->service->delete($dataApp['id']),
                ['message' => 'Ok.'],
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при удалении представителя стенда', $e);
        }
    }
}
