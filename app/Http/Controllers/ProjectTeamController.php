<?php

namespace App\Http\Controllers;

use App\Enums\SpeakerTypesEnum;
use App\Http\Requests\Speaker\ListSpeakerRequest;
use App\Http\Resources\ProjectTeam\ProjectTeamCollection;
use App\Http\Responses\ApiErrorResponse;
use App\Services\SpeakerService;
use OpenApi\Annotations as OA;
use Throwable;

class ProjectTeamController extends Controller
{

    public SpeakerTypesEnum $type;

    public function __construct(public SpeakerService $speakerService)
    {
        $this->type = SpeakerTypesEnum::PROJECT_TEAM();
    }

    /**
     * @OA\Get(
     *       path="/api/v1/project-team",
     *       operationId="AppProjectTeamList",
     *       tags={"App|Команда проекта"},
     *       summary="Получить список, всех участников команда проектаов",
     *       @OA\Parameter(
     *          name="filter[id]",
     *          in="query",
     *          description="ID участника команды проекта",
     *          @OA\Schema(
     *             type="integer",
     *          )
     *      ),
     *      @OA\Parameter(
     *         name="filter[event_id]",
     *         in="query",
     *         description="ID события",
     *         @OA\Schema(
     *            type="integer",
     *         )
     *       ),
     *      @OA\Parameter(
     *           name="filter[name]",
     *           in="query",
     *           description="Имя участника команды проекта",
     *           @OA\Schema(
     *              type="string",
     *           )
     *        ),
     *      @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Сортировка по поле",
     *         @OA\Schema(
     *            type="string",
     *            enum={"id","sort_id","name","description","created_at", "updated_at"},
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="order_by",
     *         in="query",
     *         description="Порядок сортировки",
     *         @OA\Schema(
     *            type="string",
     *            enum={"ASC", "DESC"},
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="Номер страницы",
     *          @OA\Schema(
     *                type="integer",
     *                example=1
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          description="Количество элементов на странице",
     *          @OA\Schema(
     *              type="integer",
     *              example=10
     *           )
     *       ),
     *     @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            @OA\Property(
     *              property="data",
     *              description="ID Участника команды",
     *              type="array",
     *              @OA\Items(
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="name", description="ФИО участника команды проекта", type="object",
     *                     @OA\Property(property="ru", example="Иванов Иван Василевич")
     *                 ),
     *                 @OA\Property(property="description",description="Описание участника команды проекта",
     *                     type="object",
     *                     @OA\Property(property="ru", example="Художник")
     *                 ),
     *                 @OA\Property(property="image",description="Изображение участника команды проекта",type="string",example="http://newapiartrussiafair/api/v1/"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="deleted_at", type="string", example=null),
     *                 @OA\Property(property="eventgable", type="array",
     *                     @OA\Items(
     *                        @OA\Property(property="event_id", example=1, type="integer"),
     *                        @OA\Property(property="eventgable_type", example="App\\Models\\Partner", type="string"),
     *                        @OA\Property(property="eventgable_id", example=1, type="integer"),
     *                  ),
     *               ),
     *             ),
     *          ),
     *          @OA\Property(property="links", type="object",
     *              @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/project-team/4"),
     *              ),
     *           ),
     *         ),
     *      ),
     *      @OA\Response(response=401,description="Unauthenticated"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404,description="Not found"),
     *      @OA\Response(response=403,description="Forbidden",
     *         @OA\JsonContent(
     *            @OA\Property(property="errors", type="array",
     *            @OA\Items(
     *               @OA\Property(property="status", example="403"),
     *               @OA\Property(property="detail", example="User does not have the right roles.")
     *            ),
     *         ),
     *      ),
     *    ),
     *    @OA\Response(response=500,description="Server error, not found")
     *    )
     */
    public function list(ListSpeakerRequest $request): ProjectTeamCollection|ApiErrorResponse
    {
        $dataApp = $request->validated();
        $dataApp['type'] = $this->type;
        try {
            return new ProjectTeamCollection($this->speakerService->list($dataApp));
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при выводе списока Команда проектаов', $e);
        }
    }
}
