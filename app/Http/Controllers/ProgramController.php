<?php

namespace App\Http\Controllers;

use App\Http\Requests\Speaker\ListSpeakerRequest;
use App\Http\Resources\Speaker\SpeakerCollection;
use App\Http\Responses\ApiErrorResponse;
use App\Services\ProgramService;
use OpenApi\Annotations as OA;
use Throwable;

class ProgramController extends Controller
{
    public function __construct(protected ProgramService $programService)
    {
    }

    /**
     * @OA\Get(
     *    path="/api/v1/programs",
     *    operationId="AppProgramList",
     *    tags={"App|Программа"},
     *    summary="Получить список всех программ",
     *    @OA\Parameter(
     *       name="filter[category]",
     *       in="query",
     *       description="Фильтр по category событие, пока modern|classic",
     *       required=false,
     *       @OA\Schema(
     *          type="string",
     *       )
     *     ),
     *    @OA\Parameter(
     *       name="filter[year]",
     *       in="query",
     *       description="Год событии",
     *       @OA\Schema(
                type="integer",
     *           )
     *         ),
     *    @OA\Parameter(
     *       name="filter[event_type]",
     *       in="query",
     *       description="Тип события",
     *        @OA\Schema(
     *        enum={"artForum","masterClassAdult","masterClassChild","expertTable"}
     *        )
     *    ),
     *    @OA\Parameter(
     *       name="filter[event_id]",
     *       in="query",
     *       description="ID события",
     *       @OA\Schema(
     *          type="integer",
     *       )
     *    ),
     *    @OA\Parameter(
     *       name="filter[date]",
     *       in="query",
     *       description="Дата программы",
     *       @OA\Schema(
     *          type="string",
     *       )
     *       ),
     *    @OA\Parameter(
     *       name="sort_by",
     *       in="query",
     *       description="Сортировка по поле",
     *       @OA\Schema(
     *          type="string",
     *          enum={"id","start_time","end_time","created_at", "updated_at"},
     *       )
     *    ),
     *    @OA\Parameter(
     *       name="order_by",
     *       in="query",
     *       description="Порядок сортировки",
     *       @OA\Schema(
     *          type="string",
     *          enum={"ASC", "DESC"},
     *       )
     *    ),
     *    @OA\Parameter(
     *       name="page",
     *       in="query",
     *       description="Номер страницы",
     *       @OA\Schema(
     *          type="integer",
     *          example=1
     *       )
     *    ),
     *    @OA\Parameter(
     *       name="per_page",
     *       in="query",
     *       description="Количество элементов на странице",
     *       @OA\Schema(
     *          type="integer",
     *          example=10
     *       )
     *    ),
     *   @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\MediaType(
     *             mediaType="application/vnd.api+json",
     *             @OA\Schema(
     *                type="object",
     *                @OA\Property(
     *                   property="data",
     *                   description="ID программа",
     *                   type="object",
     *                   @OA\Property(property="id", example="1", type="integer"),
     *                   @OA\Property(property="type", example="program", type="string"),
     *                   @OA\Property(property="attributes", type="object",
     *                       @OA\Property(property="event_id", description="ID события", type="integer", example="1"),
     *                       @OA\Property(property="start_time",description="Время,начало программы",type="string",example="15:30:00"),
     *                       @OA\Property(property="end_time",description="Время,конец программы",type="string",example="14:30:00"),
     *                       @OA\Property(property="date", type="sting", example="2024-11-03"),
     *                  @OA\Property(property="name", description="Название программы", type="object",
     *                     @OA\Property(property="ru", example="Российский арт-рынок"),
     *                     @OA\Property(property="en", example="Russian art program")
     *                  ),
     *                  @OA\Property(property="moderator_name", description="ФИЩ модератора", type="object",
     *                     @OA\Property(property="ru", example="Василий Пупкин"),
     *                     @OA\Property(property="en", example="John")
     *                  ),
     *                  @OA\Property(property="moderator_description", description="ФИЩ модератора", type="object",
     *                     @OA\Property(property="ru", example="Василий Пупкин крутой модератор"),
     *                     @OA\Property(property="en", example="John moderator")
     *                   ),
     *                       @OA\Property(property="speaker_id", type="array",
     *                          example={1},
     *                       @OA\Items(
     *                          type="integer"
     *                        ),
     *                      ),
     *                   @OA\Property(property="program_format", description="Формат программы", type="string", example="lecture"),
     *                   @OA\Property(property="description", description="Описание программы", type="object",
     *                        @OA\Property(property="ru", example="Прорграмма"),
     *                        @OA\Property(property="en", example="program")
     *                    ),
     *                      @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                      @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                   ),
     *                   @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12"),
     *                ),
     *                @OA\Property(property="relationships", type="object",
     *                    @OA\Property(property="events", type="object",
     *                       @OA\Property(property="data",type="object"),
     *                          @OA\Property(property="links",type="object",
     *                          @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12/relationships/event"),
     *                          @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/program/12/event" ),
     *                       ),
     *                   ),
     *                   @OA\Property(property="speakers", type="object",
     *                       @OA\Property(property="data",type="object"),
     *                       @OA\Property(property="links",type="object",
     *                           @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12/relationships/speakers"),
     *                           @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/program/12/speakers" ),
     *                       ),
     *                   ),
     *               ),
     *           ),
     *               @OA\Property(property="metadata",type="object",
     *               @OA\Property(property="message", example="Ok"),
     *               ),
     *            ),
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
     * @param ListSpeakerRequest $request
     * @return SpeakerCollection|ApiErrorResponse
     */
    public function list(ListSpeakerRequest $request): SpeakerCollection|ApiErrorResponse
    {
        $dataApp = $request->validated();
        try {
            return new SpeakerCollection($this->programService->list($dataApp));
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при выводе списока спикеров', $e);
        }
    }
}
