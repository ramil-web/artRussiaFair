<?php

namespace App\Http\Controllers;

use App\Enums\SpeakerTypesEnum;
use App\Http\Requests\Speaker\ListSpeakerRequest;
use App\Http\Requests\Speaker\ShowSpeakerRequest;
use App\Http\Resources\Speaker\SpeakerCollection;
use App\Http\Resources\Speaker\SpeakerResource;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\SpeakerService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SpeakerController extends Controller
{
    private SpeakerTypesEnum $type;

    public function __construct(public SpeakerService $speakerService)
    {
        $this->type = SpeakerTypesEnum::SPEAKER();
    }

    /**
     * @OA\Get(
     *    path="/api/v1/speakers",
     *    operationId="AppSpeakerList",
     *    tags={"App|Спикеры"},
     *    summary="Получить список всех спикеров",
     *    @OA\Parameter(
     *        name="filter[category]",
     *        in="query",
     *        description="Категория события",
     *        required=false,
     *        @OA\Schema(
     *           type="string",
     *          )
     *       ),
     *    @OA\Parameter(
     *       name="filter[id]",
     *       in="query",
     *       description="ID спикера",
     *       @OA\Schema(
     *          type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *          name="filter[event_id]",
     *          in="query",
     *          description="ID события",
     *          @OA\Schema(
     *             type="integer",
     *          )
     *        ),
     *      @OA\Parameter(
     *          name="filter[name]",
     *          in="query",
     *          description="Имя спикера",
     *          @OA\Schema(
     *             type="string",
     *          )
     *       ),
     *       @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          description="Сортировка по поле",
     *          @OA\Schema(
     *             type="string",
     *             enum={"id","sort_id","name","description","created_at", "updated_at"},
     *          )
     *        ),
     *        @OA\Parameter(
     *           name="order_by",
     *           in="query",
     *           description="Порядок сортировки",
     *           @OA\Schema(
     *              type="string",
     *              enum={"ASC", "DESC"},
     *           )
     *        ),
     *       @OA\Parameter(
     *            name="page",
     *            in="query",
     *            description="Номер страницы",
     *            @OA\Schema(
     *               type="integer",
     *               example=1
     *           )
     *         ),
     *       @OA\Parameter(
     *           name="per_page",
     *           in="query",
     *           description="Количество элементов на странице",
     *           @OA\Schema(
     *              type="integer",
     *              example=10
     *          )
     *        ),
     *      @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            @OA\Property(
     *              property="data",
     *              description="ID спикера",
     *              type="array",
     *              @OA\Items(
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="name", description="ФИО спикера", type="object",
     *                     @OA\Property(property="ru", example="Иванов Иван Василевич")
     *                 ),
     *                 @OA\Property(property="description", description="Описание спикера", type="object",
     *                     @OA\Property(property="ru", example="cпикер")
     *                 ),
     *                @OA\Property(property="full_description", description="Полное описание", type="object",
     *                   @OA\Property(property="ru", example="Спикер"),
     *                   @OA\Property(property="en", example="Speaker")
     *                ),
     *                 @OA\Property(property="image",description="Изоброжение спикера",type="string",example="http://newapiartrussiafair/api/v1/"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="deleted_at", type="string", example=null),
     *                 @OA\Property(property="eventgable", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="event_id", example=1, type="integer"),
     *                         @OA\Property(property="eventgable_type", example="App\\Models\\Partner", type="string"),
     *                         @OA\Property(property="eventgable_id", example=1, type="integer"),
     *                     ),
     *                  ),
     *              ),
     *           ),
     *           @OA\Property(property="links", type="object",
     *              @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/speaker/4"),
     *             ),
     *          ),
     *        ),
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
     * @param ListSpeakerRequest $request
     * @return SpeakerCollection|ApiErrorResponse
     */
    public function list(ListSpeakerRequest $request): SpeakerCollection|ApiErrorResponse
    {
        $dataApp = $request->validated();
        $dataApp['type'] = $this->type;
        try {
            return new SpeakerCollection($this->speakerService->list($dataApp));
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при выводе списока спикеров', $e);
        }
    }

    /**
     * @OA\Get(
     *       path="/api/v1/speaker",
     *       operationId="AppShowSpeakers",
     *       tags={"App|Спикеры"},
     *       summary="Получить данные спикера",
     *       @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="ID спикера",
     *         @OA\Schema(
     *               type="integer",
     *               example=1,
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
     *               description="ID спикера",
     *               type="object",
     *               @OA\Property(property="id", example="1", type="integer"),
     *               @OA\Property(property="type", example="speaker", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *                  @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *               @OA\Property(property="name", description="ФИО чудожника", type="object",
     *                  @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                  @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *               ),
     *               @OA\Property(property="description", description="Описание художника", type="object",
     *                  @OA\Property(property="ru", example="Художник"),
     *                  @OA\Property(property="en", example="Artist")
     *               ),
     *               @OA\Property(property="full_description", description="Полное описание", type="object",
     *                    @OA\Property(property="ru", example="Спикер"),
     *                    @OA\Property(property="en", example="Speaker")
     *                 ),
     *               @OA\Property(property="image",description="Изоброжение спикера",type="string",example="http://newapiartrussiafair/api/v1/admin/"),
     *               @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *               @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *               @OA\Property(property="deleted_at", type="string", example=null),
     *               ),
     *                @OA\Property(property="links", type="object",
     *                    @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/speaker/4"),
     *                 ),
     *               @OA\Property(property="relationships", type="object",
     *                  @OA\Property(property="events", type="object",
     *                    @OA\Property(property="data",type="object"),
     *                    @OA\Property(property="links",type="object",
     *                       @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/speaker/6/relationships/eventgable"),
     *                       @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/speaker/6/eventgable" ),
     *                   ),
     *                  ),
     *                ),
     *              ),
     *                  @OA\Property(property="metadata",type="object",
     *                     @OA\Property(property="message", example="Ok"),
     *                   ),
     *                ),
     *               ),
     *             ),
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
     * @param ShowSpeakerRequest $request
     * @return ApiSuccessResponse
     */
    public function show(ShowSpeakerRequest $request): ApiSuccessResponse
    {
        $appData = $request->validated();
        return new ApiSuccessResponse(
            new  SpeakerResource($this->speakerService->show($appData['id'])),
            ['message' => 'Ok'],
            Response::HTTP_OK
        );
    }
}
