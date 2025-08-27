<?php

namespace Lk\Http\Controllers;
use App\Enums\PersonTypesEnum;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Auth;
use Lk\Http\Requests\Person\PersonUserApplicationAccessRequest;
use Lk\Http\Requests\Person\StorePersonRequest;
use Lk\Http\Requests\Person\UpdatePersonRequest;
use Lk\Http\Resources\Worker\WorkerCollection;
use Lk\Http\Resources\Worker\WorkerResource;
use Lk\Repositories\UserApplication\UserApplicationRepository;
use Lk\Services\PersonService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class WorkerController extends Controller
{
    private PersonTypesEnum $type;

    public function __construct(
        public PersonService             $personService,
        public UserApplicationRepository $userApplicationRepository)
    {
        $this->type = PersonTypesEnum::WORKER();
    }

    /**
     * @OA\Get(
     *      path="/api/v1/lk/worker/list",
     *      tags={"Lk|Рабочие"},
     *      security={{"bearerAuth":{}}},
     *      summary="Список рабочих",
     *      operationId="LkGetWorkers",
     *      @OA\Parameter(
     *          name="filter[id]",
     *          in="query",
     *          description="Фильтр по id",
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *      @OA\Parameter(
     *         name="filter[user_application_id]",
     *         in="query",
     *         description="Фильтр по id Заявки",
     *         @OA\Schema(
     *               type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *            mediaType="application/vnd.api+json",
     *            @OA\Schema(
     *                type="object",
     *                @OA\Property(
     *                    property="data",
     *                    type="array",
     *                    @OA\Items(
     *                    @OA\Property(property="id", type="integer", example="1"),
     *                    @OA\Property(property="worker", type="string", example="worker"),
     *                    @OA\Property(
     *                       property="attributes",
     *                       type="object",
     *                       @OA\Property(property="id", type="integer", example="1"),
     *                       @OA\Property(property="user_application_id", type="integer", example="1"),
     *                       @OA\Property(property="full_name", type="object",
     *                          @OA\Property(property="ru", type="string", example="John Doe"),
     *                       ),
     *                       @OA\Property(property="passport", type="sting", example="240675847"),
     *                       @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                       @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                    ),
     *                    @OA\Property(
     *                       property="links",
     *                       type="object",
     *                       @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/lk/worker/1"),
     *                    ),
     *                    @OA\Property(property="relationships",type="object",
     *                       @OA\Property(property="user_applications",type="object"),
     *                    ),
     *                ),
     *               ),
     *                @OA\Property(property="links", type="object",
     *                @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/lk/worker/list"),
     *               ),
     *            ),
     *         ),
     *       ),
     *      @OA\Response(response=401,description="Unauthenticated"),
     *      @OA\Response(response=400,description="Bad Request"),
     *      @OA\Response(response=404,description="Not Found"),
     *      @OA\Response(response=403,description="Forbidden",
     *          @OA\JsonContent(
     *              @OA\Property(property="errors", type="array",
     *              @OA\Items(
     *                  @OA\Property(property="status", example="403"),
     *                  @OA\Property(property="detail", example="User does not have the right roles.")
     *              ),
     *           ),
     *        ),
     *     ),
     * )
     * @param PersonUserApplicationAccessRequest $request
     * @return WorkerCollection|ApiErrorResponse
     * @throws CustomException
     */
    public function list(PersonUserApplicationAccessRequest $request): WorkerCollection|ApiErrorResponse
    {
        $user = Auth::user();
        $dataApp = $request->validated();
        $dataApp['type'] = $this->type;
        return new WorkerCollection($this->personService->list($user->id, $dataApp, $this->type));
    }

    /**
     * @OA\Post(
     *    path="/api/v1/lk/worker",
     *    tags={"Lk|Рабочие"},
     *    security={{"bearerAuth":{}}},
     *    summary="Добавление рабочего",
     *    operationId="LkCreateWorker",
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *            @OA\Schema(
     *               type="object",
     *               required={"full_name","user_application_id","passport","email"},
     *               @OA\Property(property="full_name",description="Полное имя", type="string",  example="John Doe"),
     *               @OA\Property(property="user_application_id",description="ID заявки пользователя", type="integer", example=1),
     *               @OA\Property(property="passport",description="Организация", type="string", example="5503123912"),
     *            ),
     *        ),
     *     ),
     *     @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="data",
     *                   type="object",
     *                   @OA\Property(property="id", type="integer", example="1"),
     *                   @OA\Property(property="worker", type="string", example="worker"),
     *                   @OA\Property(
     *                      property="attributes",
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example="1"),
     *                      @OA\Property(property="user_application_id", type="integer", example="1"),
     *                      @OA\Property(property="full_name", type="object",
     *                         @OA\Property(property="ru", type="string", example="John Doe"),
     *                      ),
     *                      @OA\Property(property="passport", type="sting", example="240675847"),
     *                      @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                      @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                   ),
     *                   @OA\Property(
     *                      property="links",
     *                      type="object",
     *                      @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/lk/worker/1"),
     *                   ),
     *                   @OA\Property(property="relationships",type="object",
     *                      @OA\Property(property="user_applications",type="object"),
     *                   ),
     *               ),
     *               @OA\Property(property="metadata", type="object",
     *               @OA\Property(property="message", type="string", example="Рабочий успешно добавлен"),
     *              ),
     *           ),
     *        ),
     *      ),
     *     @OA\Response(response=401,description="Unauthenticated"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=404,description="Not Found"),
     *     @OA\Response(response=403,description="Forbidden",
     *         @OA\JsonContent(
     *            @OA\Property(property="errors", type="array",
     *            @OA\Items(
     *               @OA\Property(property="status", example="403"),
     *               @OA\Property(property="detail", example="User does not have the right roles.")
     *            ),
     *         ),
     *      ),
     *   ),
     * )
     * @param StorePersonRequest $request
     * @return ApiErrorResponse|ApiSuccessResponse
     */
    public function store(StorePersonRequest $request): ApiErrorResponse|ApiSuccessResponse
    {
        try {
            $dataApp = $request->validated();
            $dataApp['type'] = $this->type;
            $vipGuest = $this->personService->create($dataApp);
            return new ApiSuccessResponse(new WorkerResource($vipGuest), ['message' => 'Рабочий успешно добавлен'],ResponseAlias::HTTP_OK);
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при добавлении рабочего', $e);
        }

    }

    /**
     * @OA\Get(
     *    path="/api/v1/lk/worker/{id}",
     *    tags={"Lk|Рабочие"},
     *    security={{"bearerAuth":{}}},
     *    summary="Просмотр рабочего",
     *    operationId="LkGetWorker",
     *    @OA\Parameter(
     *           name="id",
     *           in="path",
     *           required=true,
     *           @OA\Schema(
     *               type="integer",
     *               example="1"
     *           )
     *       ),
     *      @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *            mediaType="application/vnd.api+json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="worker", type="string", example="worker"),
     *                  @OA\Property(
     *                     property="attributes",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example="1"),
     *                     @OA\Property(property="user_application_id", type="integer", example="1"),
     *                     @OA\Property(property="full_name", type="object",
     *                        @OA\Property(property="ru", type="string", example="John Doe"),
     *                     ),
     *                     @OA\Property(property="passport", type="sting", example="240675847"),
     *                     @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                     @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                  ),
     *                  @OA\Property(
     *                     property="links",
     *                     type="object",
     *                     @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/lk/worker/1")
     *                  ),
     *                  @OA\Property(property="relationships",type="object",
     *                     @OA\Property(property="user_applications",type="object"),
     *                  ),
     *              ),
     *              @OA\Property(property="metadata", type="object",
     *              @OA\Property(property="message", type="string", example="Ok"),
     *             ),
     *          ),
     *       ),
     *     ),
     *     @OA\Response(response=401,description="Unauthenticated"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=404,description="Not Found"),
     *     @OA\Response(response=403,description="Forbidden",
     *         @OA\JsonContent(
     *            @OA\Property(property="errors", type="array",
     *            @OA\Items(
     *               @OA\Property(property="status", example="403"),
     *               @OA\Property(property="detail", example="User does not have the right roles.")
     *            ),
     *         ),
     *      ),
     *   ),
     * )
     * @param int $id
     * @return ApiErrorResponse|WorkerCollection
     * @throws CustomException
     */
    public function show(int $id): WorkerCollection|ApiSuccessResponse
    {
        $user = Auth::user();
        return new ApiSuccessResponse(
            new WorkerResource($this->personService->show($id, $user->id, $this->type)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Patch(
     *    path="/api/v1/lk/worker/update/{id}",
     *    tags={"Lk|Рабочие"},
     *    security={{"bearerAuth":{}}},
     *    summary="Редактироване рабочего",
     *    operationId="LkUpdateWorker",
     *    @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       @OA\Schema(
     *          type="integer",
     *           example="1",
     *          )
     *     ),
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *            @OA\Schema(
     *               type="object",
     *               required={"full_name","user_application_id","passport","email"},
     *               @OA\Property(property="full_name",description="Полное имя", type="string",  example="John Doe"),
     *               @OA\Property(property="user_application_id",description="ID заявки пользователя", type="integer", example=1),
     *               @OA\Property(property="passport",description="Организация", type="string", example="5503123912"),
     *            ),
     *        ),
     *     ),
     *     @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example="1"),
     *                 @OA\Property(property="worker", type="string", example="worker"),
     *                 @OA\Property(
     *                    property="attributes",
     *                    type="object",
     *                    @OA\Property(property="id", type="integer", example="1"),
     *                    @OA\Property(property="user_application_id", type="integer", example="1"),
     *                    @OA\Property(property="full_name", type="object",
     *                       @OA\Property(property="ru", type="string", example="John Doe"),
     *                    ),
     *                    @OA\Property(property="passport", type="sting", example="240675847"),
     *                    @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                    @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                ),
     *                @OA\Property(
     *                   property="links",
     *                   type="object",
     *                   @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/lk/worker/1")
     *                ),
     *                @OA\Property(property="relationships",type="object",
     *                   @OA\Property(property="user_applications",type="object"),
     *                ),
     *            ),
     *            @OA\Property(property="metadata", type="object",
     *            @OA\Property(property="message", type="string", example="Ok"),
     *            ),
     *          ),
     *        ),
     *      ),
     *     @OA\Response(response=401,description="Unauthenticated"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=404,description="Not Found"),
     *     @OA\Response(response=403,description="Forbidden",
     *         @OA\JsonContent(
     *            @OA\Property(property="errors", type="array",
     *            @OA\Items(
     *               @OA\Property(property="status", example="403"),
     *               @OA\Property(property="detail", example="User does not have the right roles.")
     *            ),
     *         ),
     *      ),
     *   ),
     * )
     * @param int $id
     * @param UpdatePersonRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function update(int $id, UpdatePersonRequest $request): ApiSuccessResponse
    {
        try {
            $user = Auth::user();
            $dataApp = $request->validated();
            $response = $this->personService->update($id, $user->id, $dataApp);
            return new ApiSuccessResponse(new WorkerResource($response),['message' => 'Ok'],ResponseAlias::HTTP_OK);
        } catch (CustomException $e) {
            throw  new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }
}
