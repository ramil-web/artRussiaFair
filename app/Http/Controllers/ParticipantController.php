<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Requests\Participant\ParticipantsRequest;
use App\Http\Requests\Participant\ShowParticipantRequest;
use App\Http\Resources\Participant\ParticipantCollection;
use App\Http\Resources\Participant\ParticipantResource;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\ParticipantService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ParticipantController extends Controller
{
    public function __construct(
        protected ParticipantService $participantService
    ) {}

    /**
     * @OA\Get(
     *    path="/api/v1/participants",
     *    operationId="App|Партнёры",
     *    tags={"App|Участники"},
     *    summary="Получить список участников",
     *    @OA\Parameter(
     *         name="filter[category]",
     *         in="query",
     *         description="Категория события",
     *         required=false,
     *         @OA\Schema(
     *            type="string",
     *           )
     *        ),
     *    @OA\Parameter(
     *       name="filter[slug]",
     *       in="query",
     *       description="Слаг участника",
     *       @OA\Schema(
     *             type="string",
     *         ),
     *      ),
     *     @OA\Parameter(
     *         name="filter[event_id]",
     *         in="query",
     *         description="ID события",
     *         @OA\Schema(
     *            type="integer",
     *         )
     *       ),
     *      @OA\Parameter(
     *         name="category[]",
     *         in="query",
     *         description="Категория (тип) участника",
     *         @OA\Schema(
     *              type="array",
     *              @OA\Items(type="enum", enum={"artist", "sculptor","gallery", "photographer"}),
     *              example={"gallery", "artist"}
     *         ),
     *      ),
     *      @OA\Parameter(
     *         name="filter[name]",
     *         in="query",
     *         description="Имя участника",
     *         @OA\Schema(
     *            type="string",
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Сортировка по поле",
     *         @OA\Schema(
     *            type="string",
     *            enum={"id","sort_id","name","description","stand_id","created_at", "updated_at"},
     *          )
     *      ),
     *      @OA\Parameter(
     *         name="order_by",
     *         in="query",
     *         description="Порядок сортировки",
     *         @OA\Schema(
     *            type="string",
     *            enum={"ASC", "DESC"},
     *          )
     *       ),
     *      @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Номер страницы",
     *          @OA\Schema(
     *                type="integer",
     *                example=1
     *            )
     *       ),
     *      @OA\Parameter(
     *           name="per_page",
     *           in="query",
     *           description="Количество элементов на странице",
     *           @OA\Schema(
     *              type="integer",
     *               example=10
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
     *              description="ID участника",
     *              type="array",
     *              @OA\Items(
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="artist", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="slug", type="string", example="artist_1"),
     *                 @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                      @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                      @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *                  ),
     *                  @OA\Property(property="description", description="Описание художника", type="object",
     *                      @OA\Property(property="ru", example="Художник"),
     *                      @OA\Property(property="en", example="Artist")
     *                  ),
     *                 @OA\Property(property="image",description="Изоброжение участника",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *                 @OA\Property(property="images",description="Картинки художника",type="array",
     *                    @OA\Items(
     *                       @OA\Property(property="link", type="string",example="http://newapiartrussiafair/api/v1/artist/nature.jpg"),
     *                       @OA\Property(property="name", type="string",example="nature"),
     *                      ),
     *                 ),
     *                 @OA\Property(property="stand_id",description="Номер стенда",type="string",example="2a"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="deleted_at", type="string", example=null),
     *              ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/artist/4"),
     *                ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/artist/6/relationships/eventgable"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/artist/6/eventgable" ),
     *                  ),
     *                 ),
     *               ),
     *             ),
     *           ),
     *           @OA\Property(property="links", type="object"),
     *           @OA\Property(property="meta",type="object",),
     *           ),
     *          ),
     *       ),
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
    public function list(ParticipantsRequest $request): ParticipantCollection|ApiErrorResponse
    {
        $dataApp = $request->validated();
        try {
            return new ParticipantCollection($this->participantService->list($dataApp));
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при выводе списока участников', $e);
        }
    }

    /**
     * @OA\Get(
     *       path="/api/v1/participant",
     *       operationId="AppShowParticipantData",
     *       tags={"App|Участники"},
     *       summary="Получить данные участника",
     *       @OA\Parameter(
     *         name="slug",
     *         in="query",
     *         required=true,
     *         description="Слаг участника",
     *         @OA\Schema(
     *               type="string",
     *               example="slug",
     *           )
     *       ),
     *      @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *               property="data",
     *               description="ID участника",
     *               type="object",
     *               @OA\Property(property="id", example="1", type="integer"),
     *               @OA\Property(property="type", example="artist", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *                  @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                      @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                      @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *                  ),
     *                  @OA\Property(property="description", description="Описание художника", type="object",
     *                      @OA\Property(property="ru", example="Художник"),
     *                      @OA\Property(property="en", example="Artist")
     *                  ),
     *                  @OA\Property(property="image",description="Изоброжение участника",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *                  @OA\Property(property="stand_id",description="Номер стенда",type="string",example="2a"),
     *                  @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                  @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                  @OA\Property(property="deleted_at", type="string", example=null),
     *                  @OA\Property(property="slug", type="string", example="artist_1"),
     *                  @OA\Property(property="images",description="Картинки художника",type="array",
     *                       @OA\Items(
     *                          @OA\Property(property="link", type="string",example="http://newapiartrussiafair/api/v1/artist/nature.jpg"),
     *                          @OA\Property(property="name", type="string",example="nature"),
     *                       ),
     *                  ),
     *               ),
     *                @OA\Property(property="links", type="object",
     *                    @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/artist/4"),
     *                 ),
     *               @OA\Property(property="relationships", type="object",
     *                  @OA\Property(property="events", type="object",
     *                    @OA\Property(property="data",type="object"),
     *                    @OA\Property(property="links",type="object",
     *                       @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/artist/6/relationships/eventgable"),
     *                       @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/artist/6/eventgable" ),
     *                  ),
     *                ),
     *              ),
     *            ),
     *            @OA\Property(property="links", type="object"),
     *            @OA\Property(property="meta",type="object",),
     *            ),
     *           ),
     *        ),
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
     * @param ShowParticipantRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function show(ShowParticipantRequest $request): ApiSuccessResponse
    {
        $appData = $request->validated();
        return new ApiSuccessResponse(
            new  ParticipantResource($this->participantService->show($appData['slug'])),
            ['message' => 'Ok'],
            Response::HTTP_OK
        );
    }
}
