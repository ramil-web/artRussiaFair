<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\Program\ListProgramRequest;
use Admin\Http\Requests\Program\StoreProgramRequest;
use Admin\Http\Requests\Program\UpdateProgramRequest;
use Admin\Http\Resources\Artist\ArtistResource;
use Admin\Http\Resources\Program\ProgramCollection;
use Admin\Http\Resources\Program\ProgramResource;
use Admin\Services\ParticipantService;
use Admin\Services\ProgramService;
use App\Exceptions\CustomException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class ProgramController extends Controller
{
    public function __construct(protected ProgramService $programService)
    {
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/program/store",
     *      operationId="AdminProgramSore",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Программа"},
     *      summary="Добавление программы",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            required={"name", "event_id", "moderator_name", "start_time", "end_time"},
     *            @OA\Property(property="event_id", description="ID события", type="integer", example="1"),
     *            @OA\Property(property="program_format", description="Формат программы", type="string", example="lecture"),
     *            @OA\Property(property="start_time", description="Время, начало программы", type="string", example="15:30:00"),
     *            @OA\Property(property="end_time", description="Время, конец программы", type="string", example="14:30:00"),
     *            @OA\Property(property="date", type="sting", example="2024-11-03"),
     *            @OA\Property(property="name", description="Название программы", type="object",
     *               @OA\Property(property="ru", example="Российский арт-рынок"),
     *               @OA\Property(property="en", example="Russian art program")
     *            ),
     *            @OA\Property(property="moderator_name", description="ФИЩ модератора", type="object",
     *               @OA\Property(property="ru", example="Василий Пупкин"),
     *               @OA\Property(property="en", example="John")
     *             ),
     *            @OA\Property(property="moderator_description", description="ФИЩ модератора", type="object",
     *                @OA\Property(property="ru", example="Василий Пупкин крутой модератор"),
     *                @OA\Property(property="en", example="John moderator")
     *              ),
     *            @OA\Property(property="description", description="Описание программы", type="object",
     *                      @OA\Property(property="ru", example="Прорграмма"),
     *                      @OA\Property(property="en", example="program")
     *                  ),
     *            @OA\Property(property="speaker_id", type="array",
     *                   example={1},
     *                   @OA\Items(
     *                        type="integer"
     *                   ),
     *               ),
     *             @OA\Property(property="partners_id", type="array",
     *                    example={1},
     *                    @OA\Items(
     *                         type="integer"
     *                    ),
     *                ),
     *            @OA\Property(property="locate", description="Язык", type="string", example="ru"),
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
     *              @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *              @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *              @OA\Property(property="program_format", description="Формат программы", type="string", example="lecture"),
     *              @OA\Property(property="description", description="Описание программы", type="object",
     *                       @OA\Property(property="ru", example="Прорграмма"),
     *                       @OA\Property(property="en", example="program")
     *                   ),
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
     * @param StoreProgramRequest $request
     * @return ApiErrorResponse|ApiSuccessResponse
     */
    public function store(StoreProgramRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $dataApp = $request->validated();
            return new ApiSuccessResponse(
                new  ProgramResource($this->programService->create($dataApp)),
                ['message' => 'Программа успешно добавлена'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'Ошибка при добавление программы',
                $exception
            );
        }
    }

    /**
     * @OA\Get(
     *       path="/api/v1/admin/program/{id}",
     *       operationId="AdminGetprogram",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Программа"},
     *       summary="Получить данные программы",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID программы",
     *         @OA\Schema(
     *               type="integer",
     *               example="1",
     *           )
     *       ),
     *       @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            @OA\Property(
     *              property="data",
     *              description="ID программа",
     *              type="object",
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="program", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                    @OA\Property(property="start_time",description="Время, начало программы",type="string",example="15:30:00"),
     *                    @OA\Property(property="end_time",description="Время, конец программы",type="string",example="14:30:00"),
     *                    @OA\Property(property="date", type="sting", example="2024-11-03"),
     *              @OA\Property(property="name", description="Название программы", type="object",
     *                  @OA\Property(property="ru", example="Российский арт-рынок"),
     *                  @OA\Property(property="en", example="Russian art program")
     *              ),
     *               @OA\Property(property="moderator_name", description="ФИЩ модератора", type="object",
     *                 @OA\Property(property="ru", example="Василий Пупкин"),
     *                  @OA\Property(property="en", example="John")
     *              ),
     *             @OA\Property(property="moderator_description", description="ФИЩ модератора", type="object",
     *                 @OA\Property(property="ru", example="Василий Пупкин крутой модератор"),
     *                 @OA\Property(property="en", example="John moderator")
     *               ),
     *            @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *            @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *            @OA\Property(property="event", description="события", type="object"),
     *            @OA\Property(property="program_format", description="Формат программы", type="string", example="lecture"),
     *            @OA\Property(property="description", description="Описание программы", type="object",
     *                       @OA\Property(property="ru", example="Прорграмма"),
     *                       @OA\Property(property="en", example="program")
     *                   ),
     *            @OA\Property(property="speaker", type="array",
     *                       @OA\Items(type="object",
     *                       ),
     *                    ),
     *              ),
     *              @OA\Property(property="links", type="object",
     *                  @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12"),
     *               ),
     *              @OA\Property(property="relationships", type="object",
     *                 @OA\Property(property="events", type="object",
     *                   @OA\Property(property="data",type="object"),
     *                   @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12/relationships/event"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/program/12/event" ),
     *                  ),
     *                 ),
     *                 @OA\Property(property="speakers", type="object",
     *                      @OA\Property(property="data",type="object"),
     *                     @OA\Property(property="links",type="object",
     *                     @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12/relationships/speakers"),
     *                     @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/program/12/speakers" ),
     *                     ),
     *                    ),
     *               ),
     *             ),
     *                 @OA\Property(property="metadata",type="object",
     *                    @OA\Property(property="message", example="Ok"),
     *                  ),
     *               ),
     *              ),
     *            ),
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
     * @param int $id
     * @return ApiSuccessResponse
     */
    public function show(int $id): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            new  ProgramResource($this->programService->show($id)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/program/all",
     *    operationId="AdminProgramList",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Программа"},
     *    summary="Получить список программ",
     *    @OA\Parameter(
     *        name="filter[event_type]",
     *        in="query",
     *        description="Тип события",
     *         @OA\Schema(
     *         enum={"artForum","masterClassAdult","masterClassChild","expertTable"}
     *         )
     *     ),
     *    @OA\Parameter(
     *        name="filter[program_format]",
     *        in="query",
     *        description="По формату",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *        name="filter[speaker_id]",
     *        in="query",
     *        description="Фильтр по ID спикера",
     *        required=false,
     *        @OA\Schema(
     *          type="integer",
     *          )
     *      ),
     *    @OA\Parameter(
     *       name="filter[category]",
     *       in="query",
     *       description="Фильтр по category пока modern|classic|etc",
     *       required=false,
     *       @OA\Schema(
     *         type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *        name="filter[trashed]",
     *        in="query",
     *        description="Показать удаленных(архивных) (with/only)",
     *        @OA\Schema(
     *           type="string",
     *           enum={"with","only"},
     *         )
     *     ),
     *    @OA\Parameter(
     *       name="filter[id]",
     *       in="query",
     *       description="ID программы",
     *       @OA\Schema(
     *          type="integer",
     *       )
     *    ),
     *   @OA\Parameter(
     *      name="filter[moderator_name]",
     *      in="query",
     *      description="Имя модератора",
     *      @OA\Schema(
     *              type="string",
     *         )
     *   ),
     *   @OA\Parameter(
     *      name="filter[name]",
     *      in="query",
     *      description="Имя программа",
     *      @OA\Schema(
     *         type="string",
     *      )
     *    ),
     *   @OA\Parameter(
     *      name="order_by",
     *      in="query",
     *      description="Порядок сортировки",
     *      @OA\Schema(
     *             type="string",
     *             enum={"ASC", "DESC"},
     *        )
     *     ),
     *    @OA\Parameter(
     *       name="page",
     *       in="query",
     *       description="Номер страницы",
     *       @OA\Schema(
     *              type="integer",
     *              example=1
     *            )
     *     ),
     *   @OA\Parameter(
     *      name="per_page",
     *      in="query",
     *      description="Количество элементов на странице",
     *      @OA\Schema(
     *              type="integer",
     *              example=10
     *           ),
     *    ),
     *   @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *            mediaType="application/vnd.api+json",
     *            @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                  property="data",
     *                  description="ID программа",
     *                  type="object",
     *                  @OA\Property(property="id", example="1", type="integer"),
     *                  @OA\Property(property="type", example="program", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *                      @OA\Property(property="event_id", description="ID события", type="integer", example="1"),
     *                      @OA\Property(property="start_time",description="Время,начало программы",type="string",example="15:30:00"),
     *                      @OA\Property(property="end_time",description="Время,конец программы",type="string",example="14:30:00"),
     *                      @OA\Property(property="date", type="sting", example="2024-11-03"),
     *                 @OA\Property(property="name", description="Название программы", type="object",
     *                    @OA\Property(property="ru", example="Российский арт-рынок"),
     *                    @OA\Property(property="en", example="Russian art program")
     *                 ),
     *                 @OA\Property(property="moderator_name", description="ФИЩ модератора", type="object",
     *                    @OA\Property(property="ru", example="Василий Пупкин"),
     *                    @OA\Property(property="en", example="John")
     *                 ),
     *                 @OA\Property(property="moderator_description", description="ФИЩ модератора", type="object",
     *                    @OA\Property(property="ru", example="Василий Пупкин крутой модератор"),
     *                    @OA\Property(property="en", example="John moderator")
     *                  ),
     *                      @OA\Property(property="speaker_id", type="array",
     *                         example={1},
     *                      @OA\Items(
     *                         type="integer"
     *                       ),
     *                     ),
     *                  @OA\Property(property="program_format", description="Формат программы", type="string", example="lecture"),
     *                  @OA\Property(property="description", description="Описание программы", type="object",
     *                       @OA\Property(property="ru", example="Прорграмма"),
     *                       @OA\Property(property="en", example="program")
     *                   ),
     *                     @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                     @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                  ),
     *                  @OA\Property(property="links", type="object",
     *                  @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12"),
     *               ),
     *               @OA\Property(property="relationships", type="object",
     *                   @OA\Property(property="events", type="object",
     *                      @OA\Property(property="data",type="object"),
     *                         @OA\Property(property="links",type="object",
     *                         @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12/relationships/event"),
     *                         @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/program/12/event" ),
     *                      ),
     *                  ),
     *                  @OA\Property(property="speakers", type="object",
     *                      @OA\Property(property="data",type="object"),
     *                      @OA\Property(property="links",type="object",
     *                          @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12/relationships/speakers"),
     *                          @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/program/12/speakers" ),
     *                      ),
     *                  ),
     *              ),
     *          ),
     *              @OA\Property(property="metadata",type="object",
     *              @OA\Property(property="message", example="Ok"),
     *              ),
     *           ),
     *        ),
     *     ),
     *     @OA\Response(response=401,description="Unauthenticated"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=404,description="Not found"),
     *     @OA\Response(response=403,description="Forbidden",
     *         @OA\JsonContent(
     *            @OA\Property(property="errors", type="array",
     *            @OA\Items(
     *               @OA\Property(property="status", example="403"),
     *               @OA\Property(property="detail", example="User does not have the right roles.")
     *               ),
     *            ),
     *         ),
     *      ),
     *      @OA\Response(response=500,description="Server error, not found")
     *    )
     * @param ListProgramRequest $request
     * @return ProgramCollection|ApiErrorResponse
     */
    public function list(ListProgramRequest $request): ProgramCollection|ApiErrorResponse
    {
        $dataApp = $request->validated();
        try {
            return new ProgramCollection($this->programService->list($dataApp));
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при выводе списока программыов', $e);
        }
    }

    /**
     * @OA\Patch(
     *       path="/api/v1/admin/program/update/{id}",
     *       operationId="AdminProgramUpdate",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Программа"},
     *       summary="Редактирование данные программы",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID программа",
     *         @OA\Schema(
     *               type="integer",
     *               example="1",
     *           )
     *       ),
     *       @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *             type="object",
     *             @OA\Property(property="event_id", description="ID события", type="integer", example="1"),
     *             @OA\Property(property="program_format", description="Формат программы", type="string", example="lecture"),
     *             @OA\Property(property="start_time",description="Время,начало программы",type="string",example="15:30:00"),
     *             @OA\Property(property="end_time",description="Время,конец программы",type="string",example="14:30:00"),
     *             @OA\Property(property="date", type="sting", example="2024-11-03"),
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
     *             @OA\Property(property="speaker_id", type="array",
     *                example={1},
     *                @OA\Items(
     *                   type="integer"
     *                ),
     *            ),
     *           @OA\Property(property="description", description="Описание программы", type="object",
     *                       @OA\Property(property="ru", example="Прорграмма"),
     *                       @OA\Property(property="en", example="program")
     *                   ),
     *             @OA\Property(property="partners_id", type="array",
     *                 example={1},
     *                 @OA\Items(
     *                    type="integer"
     *                 ),
     *             ),
     *            @OA\Property(property="locate", description="Язык", type="string", example="ru"),
     *           ),
     *         ),
     *       ),
     *       @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *               property="data",
     *               description="ID программа",
     *               type="object",
     *               @OA\Property(property="id", example="1", type="integer"),
     *               @OA\Property(property="type", example="program", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *                     @OA\Property(property="start_time",description="Время,начало программы",type="string",example="15:30:00"),
     *                     @OA\Property(property="end_time",description="Время,конец программы",type="string",example="14:30:00"),
     *                     @OA\Property(property="date", type="sting", example="2024-11-03"),
     *               @OA\Property(property="name", description="Название программы", type="object",
     *                  @OA\Property(property="ru", example="Российский арт-рынок"),
     *                  @OA\Property(property="en", example="Russian art program"),
     *              ),
     *              @OA\Property(property="moderator_name", description="ФИЩ модератора", type="object",
     *                  @OA\Property(property="ru", example="Василий Пупкин"),
     *                  @OA\Property(property="en", example="John"),
     *               ),
     *               @OA\Property(property="moderator_description", description="ФИЩ модератора", type="object",
     *                 @OA\Property(property="ru", example="Василий Пупкин крутой модератор"),
     *                 @OA\Property(property="en", example="John moderator"),
     *               ),
     *               @OA\Property(property="description", description="Описание программы", type="object",
     *                       @OA\Property(property="ru", example="Прорграмма"),
     *                       @OA\Property(property="en", example="program")
     *                   ),
     *                @OA\Property(property="program_format", description="Формат программы", type="string", example="lecture"),
     *                @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                @OA\Property(property="event", description="события", type="object"),
     *                    @OA\Property(property="speaker", type="array",
     *                       @OA\Items(type="object",
     *                       ),
     *                  ),
     *               ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12"),
     *                ),
     *               @OA\Property(property="relationships", type="object",
     *                  @OA\Property(property="events", type="object",
     *                    @OA\Property(property="data",type="object"),
     *                    @OA\Property(property="links",type="object",
     *                       @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12/relationships/event"),
     *                       @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/program/12/event" ),
     *                   ),
     *                  ),
     *                  @OA\Property(property="speakers", type="object",
     *                       @OA\Property(property="data",type="object"),
     *                      @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12/relationships/speakers"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/program/12/speakers" ),
     *                      ),
     *                     ),
     *                ),
     *              ),
     *              @OA\Property(property="metadata",type="object",
     *                 @OA\Property(property="message", example="Ok"),
     *              ),
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
     * @param int $id
     * @param UpdateProgramRequest $request
     * @return ApiSuccessResponse|ResourceNotFoundException
     * @throws CustomException
     */
    public function update(int $id, UpdateProgramRequest $request): ApiSuccessResponse|ResourceNotFoundException
    {
        $dataApp = $request->validated();
        try {
            return new ApiSuccessResponse(
                new ProgramResource($this->programService->update($id, $dataApp)),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Delete(
     *       path="/api/v1/admin/program/delete/{id}",
     *       operationId="AdminProgramDelete",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Программа"},
     *       summary="Полностью удаление программы",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID программы",
     *         @OA\Schema(
     *               type="integer",
     *               example="1",
     *           )
     *       ),
     *      @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *              @OA\Property(property="data", type="boolean", example="true"),
     *              @OA\Property(property="metadata",type="object",
     *                 @OA\Property(property="message", example="Ok"),
     *              ),
     *            ),
     *         ),
     *     ),
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
     * @param int $id
     * @return ResourceNotFoundException|ApiSuccessResponse
     * @throws CustomException
     */
    public function delete(int $id): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            return new ApiSuccessResponse(
                $this->programService->delete($id, ParticipantService::DELETE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }
    /**
     * @OA\Delete(
     *       path="/api/v1/admin/program/archive/{id}",
     *       operationId="AdminProgrammArchive",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Программа"},
     *       summary="Добавить программу в архив",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID программы",
     *         @OA\Schema(
     *               type="integer",
     *               example="1",
     *           )
     *       ),
     *      @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *              @OA\Property(property="data", type="boolean", example="true"),
     *              @OA\Property(property="metadata",type="object",
     *                 @OA\Property(property="message", example="Ok"),
     *              ),
     *            ),
     *         ),
     *     ),
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
     * @param int $id
     * @return ResourceNotFoundException|ApiSuccessResponse
     * @throws CustomException
     */
    public function archive(int $id): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            return new ApiSuccessResponse(
                $this->programService->delete($id, ParticipantService::ARCHIVE),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }

    /**
     * @OA\Patch(
     *    path="/api/v1/admin/program/restore/{id}",
     *    operationId="Restoreprogram",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Программа"},
     *    summary="Восстановить программы из архива",
     *    @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       description="ID программы",
     *       @OA\Schema(
     *          type="integer",
     *          example="1",
     *       )
     *    ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *               property="data",
     *               description="ID программа",
     *               type="object",
     *               @OA\Property(property="id", example="1", type="integer"),
     *               @OA\Property(property="type", example="program", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *                     @OA\Property(property="start_time",description="Время, начало программы",type="string",example="15:30:00"),
     *                     @OA\Property(property="end_time",description="Время, конец программы",type="string",example="14:30:00"),
     *                     @OA\Property(property="date", type="sting", example="2024-11-03"),
     *               @OA\Property(property="name", description="Название программы", type="object",
     *                   @OA\Property(property="ru", example="Российский арт-рынок"),
     *                   @OA\Property(property="en", example="Russian art program")
     *               ),
     *                @OA\Property(property="moderator_name", description="ФИЩ модератора", type="object",
     *                  @OA\Property(property="ru", example="Василий Пупкин"),
     *                   @OA\Property(property="en", example="John")
     *               ),
     *              @OA\Property(property="moderator_description", description="ФИЩ модератора", type="object",
     *                  @OA\Property(property="ru", example="Василий Пупкин крутой модератор"),
     *                  @OA\Property(property="en", example="John moderator")
     *                ),
     *                 @OA\Property(property="program_format", description="Формат программы", type="string", example="lecture"),
     *                 @OA\Property(property="description", description="Описание программы", type="object",
     *                       @OA\Property(property="ru", example="Прорграмма"),
     *                       @OA\Property(property="en", example="program")
     *                   ),
     *                  @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                     @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                     @OA\Property(property="event", description="события", type="object"),
     *                     @OA\Property(property="speaker", type="array",
     *                        @OA\Items(type="object",
     *                        ),
     *                     ),
     *               ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12"),
     *                ),
     *               @OA\Property(property="relationships", type="object",
     *                  @OA\Property(property="events", type="object",
     *                    @OA\Property(property="data",type="object"),
     *                    @OA\Property(property="links",type="object",
     *                       @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12/relationships/event"),
     *                       @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/program/12/event" ),
     *                   ),
     *                  ),
     *                  @OA\Property(property="speakers", type="object",
     *                       @OA\Property(property="data",type="object"),
     *                      @OA\Property(property="links",type="object",
     *                      @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/program/12/relationships/speakers"),
     *                      @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/program/12/speakers" ),
     *                      ),
     *                     ),
     *                ),
     *              ),
     *                  @OA\Property(property="metadata",type="object",
     *                     @OA\Property(property="message", example="Ok"),
     *                   ),
     *                ),
     *               ),
     *             ),
     *       @OA\Response(response=403,description="Forbidden",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", example="User does not have the right roles.")
     *        ),
     *     ),
     *     @OA\Response(response=500,description="Server error, not found")
     *     ),
     *   ),
     * @param int $id
     * @return ApiSuccessResponse|ApiErrorResponse
     * @throws CustomException
     **/
    public function restore(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        if ($this->programService->checkData($id)) {
            $this->programService->restore($id);
            return new ApiSuccessResponse(
                new ArtistResource($this->programService->show($id)),
                ['message' => 'успешно восстановлен'],
                ResponseAlias::HTTP_OK
            );
        }
        return new ApiErrorResponse(
            'Such an artist does not exist in the archive.',
            null,
            ResponseAlias::HTTP_NOT_FOUND
        );
    }
}
