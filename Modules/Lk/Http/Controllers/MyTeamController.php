<?php

namespace Lk\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Http\JsonResponse;
use Lk\Http\Requests\MyTeam\DeleteMyTeamRequest;
use Lk\Http\Requests\MyTeam\ShowMyTeamRequest;
use Lk\Http\Requests\MyTeam\StoreMyTeamRequest;
use Lk\Http\Requests\MyTeam\UpdateMyTeamRequest;
use Lk\Http\Resources\MyTeam\MyTeamResource;
use Lk\Services\MyTeamService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class MyTeamController extends Controller
{
    public function __construct(protected MyTeamService $service)
    {
    }

    /**
     * @OA\Post(
     *    path="/api/v1/lk/my-team/store",
     *    operationId="Lk.myTeamStor",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Моя команда"},
     *    summary="Добавление команды",
     *    @OA\Parameter(
     *       name="user_application_id",
     *       in="query",
     *       required=true,
     *        description="ID заявки",
     *       @OA\Schema(
     *            type="string",
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
     *             @OA\Property(property="square",description="Площадь стенда",type="integer",example=20),
     *             @OA\Property(property="check_in",description="ID слота (заезд)",type="integer",example=1),
     *             @OA\Property(property="exit",description="ID лтоа (выезд)",type="integer",example=5),
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
     *              @OA\Property(property="type", example="my-team", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="user_application_id",description="Идентификатор заявки",type="integer",example="2"),
     *                 @OA\Property(property="check_in",description="Идентификатор слота(заезд)",type="integer",example=1),
     *                 @OA\Property(property="exit",description="Идентификатор слота (выезд)",type="integer",example=5),
     *                 @OA\Property(property="square",description="Площадь стенда",type="integer",example=20),
     *                 @OA\Property(property="status", description="Статус", type="string", example="self-employed"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *               ),
     *              @OA\Property(property="links", type="object",
     *                  @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team/show?id=3"),
     *               ),
     *              @OA\Property(property="relationships", type="object",
     *                     @OA\Property(property="user_application", type="object",
     *                     @OA\Property(property="data",type="object",example=null),
     *                     @OA\Property(property="links", type="object",
     *                         @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team/4/relationships/user_application"),
     *                         @OA\Property(property="related",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team/4/user_application"),
     *                       ),
     *                     ),
     *                   ),
     *               ),
     *                 @OA\Property(property="metadata",type="object",
     *                    @OA\Property(property="message", example="Моя команда успешно добавлена"),
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
     *      @OA\Response(response=409,description="Conflict, alredy existed",
     *           @OA\JsonContent(
     *               @OA\Property(property="errors", type="array",
     *               @OA\Items(
     *                   @OA\Property(property="status", example="409"),
     *                   @OA\Property(property="detail", example="Для этой заявки, моя команда уже существует!")
     *               ),
     *            ),
     *          ),
     *       ),
     * )
     * @param StoreMyTeamRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse|JsonResponse
     */
    public function store(StoreMyTeamRequest $request): ApiSuccessResponse|ApiErrorResponse|JsonResponse
    {
        try {
            $dataApp = $request->validated();

            /**
             * Проверяем есть ли команда привязанная к этой заявке
             */
            if ($this->service->checkMyTeam($dataApp['user_application_id'])) {
                return response()->json([
                    'errors' => [
                        [
                            'status' => ResponseAlias::HTTP_CONFLICT,
                            'detail' => 'Для этой заявки, моя команда уже существует!',
                        ],
                    ],
                ], ResponseAlias::HTTP_CONFLICT);
            }

            $documents = $this->service->store($dataApp);
            return new ApiSuccessResponse(
                new MyTeamResource($documents),
                ['message' => 'Моя команда успешно добавлена'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при добавлении команды', $e);
        }
    }

    /**
     * @OA\Get(
     *    path="/api/v1/lk/my-team/show",
     *    operationId="Lk.myTeamShow",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Моя команда"},
     *    summary="Просмотр команды",
     *    @OA\Parameter(
     *       name="user_application_id",
     *       in="query",
     *       required=true,
     *        description="ID заявки",
     *       @OA\Schema(
     *            type="string",
     *            example=1
     *       )
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
     *              @OA\Property(property="type", example="my-team", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="user_application_id",description="Идентификатор заявки",type="integer",example="2"),
     *                 @OA\Property(property="check_in",description="Идентификатор слота(заезд)",type="integer",example=1),
     *                 @OA\Property(property="exit",description="Идентификатор слота (выезд)",type="integer",example=5),
     *                 @OA\Property(property="square",description="Площадь стенда",type="integer",example=20),
     *                 @OA\Property(property="status", description="Статус", type="string", example="self-employed"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="builders", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="full_name", type="string", example="Джугашвили Василий Сталин"),
     *                         @OA\Property(property="passport", type="string", example="323234243434"),
     *                     ),
     *                 ),
     *                 @OA\Property(property="stand_representative", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="full_name", type="string", example="Джугашвили Василий Сталин"),
     *                          @OA\Property(property="passport", type="string", example="323234243434"),
     *                      ),
     *                  ),
     *               ),
     *              @OA\Property(property="links", type="object",
     *                  @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team/show?id=3"),
     *               ),
     *              @OA\Property(property="relationships", type="object",
     *                     @OA\Property(property="user_application", type="object",
     *                     @OA\Property(property="data",type="object",example=null),
     *                     @OA\Property(property="links", type="object",
     *                         @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team/4/relationships/user_application"),
     *                         @OA\Property(property="related",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team/4/user_application"),
     *                       ),
     *                     ),
     *                   ),
     *               ),
     *                 @OA\Property(property="metadata",type="object",
     *                    @OA\Property(property="message", example="Ок."),
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
     * @param ShowMyTeamRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function show(ShowMyTeamRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $dataApp = $request->validated();
            $documents = $this->service->show($dataApp['user_application_id']);
            return new ApiSuccessResponse(
                new MyTeamResource($documents),
                ['message' => 'ОК'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при получение команды', $e);
        }
    }

    /**
     * @OA\Delete(
     *    path="/api/v1/lk/my-team/delete",
     *    operationId="Lk.myTeamDelete",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Моя команда"},
     *    summary="Удаление команды поностью, вместе с застройщиками и представителями",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
     *        description="ID команды",
     *       @OA\Schema(
     *            type="string",
     *            example=1
     *       )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\MediaType(
     *            mediaType="application/vnd.api+json",
     *            @OA\Schema(
     *                @OA\Property(property="data", type="boolean", example="true"),
     *                @OA\Property(property="metadata",type="object",
     *                   @OA\Property(property="message", example="Ok"),
     *                ),
     *              ),
     *           ),
     *       ),
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
     * @param DeleteMyTeamRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function delete(DeleteMyTeamRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $dataApp = $request->validated();
            $documents = $this->service->delete($dataApp['id']);
            return new ApiSuccessResponse(
                $documents,
                ['message' => 'ОК'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при получение команды', $e);
        }
    }

    /**
     * @OA\Patch(
     *    path="/api/v1/lk/my-team/update",
     *    operationId="Lk.myTeamUpdate",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Моя команда"},
     *    summary="Редактирование команды",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
     *       description="ID команды",
     *       @OA\Schema(
     *            type="string",
     *            example=1
     *       )
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *             type="object",
     *             @OA\Property(property="square",description="Площадь стенда",type="integer",example=20),
     *             @OA\Property(property="check_in",description="ID слота (заезд)",type="integer",example=1),
     *             @OA\Property(property="exit",description="ID лтоа (выезд)",type="integer",example=5),
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
     *              @OA\Property(property="type", example="my-team", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="user_application_id",description="Идентификатор заявки",type="integer",example="2"),
     *                 @OA\Property(property="check_in",description="Идентификатор слота(заезд)",type="integer",example=1),
     *                 @OA\Property(property="exit",description="Идентификатор слота (выезд)",type="integer",example=5),
     *                 @OA\Property(property="square",description="Площадь стенда",type="integer",example=20),
     *                 @OA\Property(property="status", description="Статус", type="string", example="self-employed"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *               ),
     *              @OA\Property(property="links", type="object",
     *                  @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team/show?id=3"),
     *               ),
     *              @OA\Property(property="relationships", type="object",
     *                     @OA\Property(property="user_application", type="object",
     *                     @OA\Property(property="data",type="object",example=null),
     *                     @OA\Property(property="links", type="object",
     *                         @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team/4/relationships/user_application"),
     *                         @OA\Property(property="related",type="string", example="http://newapiartrussiafair/api/v1/lk/my-team/4/user_application"),
     *                       ),
     *                     ),
     *                   ),
     *               ),
     *                 @OA\Property(property="metadata",type="object",
     *                    @OA\Property(property="message", example="Моя команда успешно обнавлена"),
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
     * @param UpdateMyTeamRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse|JsonResponse
     */
    public function update(UpdateMyTeamRequest $request): ApiSuccessResponse|ApiErrorResponse|JsonResponse
    {
        try {
            $dataApp = $request->validated();
            if (array_key_exists('square', $dataApp) && $this->service->checkStandRepresentative($dataApp)) {
                return response()->json([
                    'errors' => [
                        [
                            'status' => ResponseAlias::HTTP_OK,
                            'detail' => 'Прежде чем уменьшать площадь стенда, необходимо уменьшить количество представителей стенда!',
                        ],
                    ],
                ], ResponseAlias::HTTP_OK);
            }

            $documents = $this->service->update($dataApp);
            return new ApiSuccessResponse(
                new MyTeamResource($documents),
                ['message' => 'Моя команда успешно обновлена'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при обнавлении команды', $e);
        }
    }
}
