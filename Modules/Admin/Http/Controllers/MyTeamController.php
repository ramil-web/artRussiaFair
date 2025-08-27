<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\MyTeam\ListMyTeamRequest;
use Admin\Services\MyTeamService;
use App\Exceptions\CustomException;
use App\Exports\MyTeamExport;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use OpenApi\Annotations as OA;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MyTeamController extends Controller
{
    public function __construct(protected MyTeamService $service)
    {
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/my-team/list",
     *    operationId="Admin.myTeamList",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Моя команда"},
     *    summary="Просмотр команд",
     *    @OA\Parameter(
     *            name="sort",
     *            in="query",
     *            description="Сортировка по поле",
     *            @OA\Schema(
     *               type="string",
     *               enum={"my_teams.id","-my_teams.id","my_teams.square", "-my_teams.square", "my_teams.user_application_id","-my_teams.user_application_id",
     *                     "my_teams.created_at","-my_teams.created_at","my_teams.updated_at","-my_teams.updated_at",
     *                     "-builders.full_name", "builders.full_name", "-stand_representatives.full_name","stand_representatives.full_name",
     *                     "user_profiles.name->>'ru'","-user_profiles.name->>'ru'","user_profiles.surname->>'ru'","-user_profiles.surname->>'ru'",
     *                     },
     *             )
     *         ),
     *       @OA\Parameter(
     *               name="page",
     *               in="query",
     *               description="Номер страницы",
     *               @OA\Schema(
     *                  type="integer",
     *                  example=1
     *              )
     *          ),
     *         @OA\Parameter(
     *             name="per_page",
     *             in="query",
     *             description="Количество элементов на странице",
     *             @OA\Schema(
     *                type="integer",
     *                 example=10
     *              )
     *         ),
     *    @OA\Response(
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
     *                 @OA\Property(property="check_in",description="Идентификатор слота(заезд)",type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="date", type="string", example="2023-11-04"),
     *                          @OA\Property(property="interval_times", type="string", example="14:30:00"),
     *                      ),
     *                  ),
     *                 @OA\Property(property="exit",description="Идентификатор слота (выезд)",type="array",
     *                       @OA\Items(
     *                           @OA\Property(property="id", type="integer", example=1),
     *                           @OA\Property(property="date", type="string", example="2023-11-04"),
     *                           @OA\Property(property="interval_times", type="string", example="14:30:00"),
     *                       ),
     *                   ),
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
     *                 @OA\Property(property="stand_representatives", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="full_name", type="object", example="Джугашвили Василий Сталин"),
     *                          @OA\Property(property="passport", type="string", example="323234243434"),
     *                      ),
     *                  ),
     *                @OA\Property(property="user_profil", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="user_id", type="integer", example=1),
     *                          @OA\Property(property="name", type="object",
     *                              @OA\Property(property="ru", example="Bасилий"),
     *                          ),
     *                          @OA\Property(property="surname", type="object",
     *                               @OA\Property(property="ru", example="Иванов"),
     *                      ),
     *                    ),
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
     *        @OA\Response(response=401, description="Unauthenticated"),
     *        @OA\Response(response=400,description="Bad Request"),
     *        @OA\Response(response=404, description="Not Found"),
     *        @OA\Response(response=403,description="Forbidden",
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
     * @param ListMyTeamRequest $request
     * @return ApiErrorResponse|Collection
     */
    public function list(ListMyTeamRequest $request):ApiErrorResponse|LengthAwarePaginator
    {
        try {
            $dataApp = $request->validated();
            return $this->service->list($dataApp);
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при получение коман', $e);
        }
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/my-team/export",
     *    operationId="exportMyTeams",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Моя команда"},
     *    summary="Экспорт команд",
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *              @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                  property="data",
     *                  type="string",
     *                  example="C:\\xampp\\htdocs\\newapiartrussiafair\\storage\\/app//my-teams/Команды-2024-11-28_06-24-25.xlsx"
     *                 ),
     *               @OA\Property(property="metadata", type="object",
     *                @OA\Property(property="message", type="string", example="Ok"),
     *               ),
     *              ),
     *            ),
     *          ),
     *     @OA\Response(response=401,description="Unauthenticated"),
     *     @OA\Response(response=400,description="Bad Request"),
     *     @OA\Response(response=404,description="not found"),
     *          @OA\Response(response=403,description="Forbidden",
     *           @OA\JsonContent(
     *               @OA\Property(property="errors", type="array",
     *                  @OA\Items(
     *                  @OA\Property(property="status", example="403"),
     *                  @OA\Property(property="detail", example="User does not have the right roles.")
     *                  ),
     *              ),
     *           ),
     *       ),
     *     @OA\Response(response=500,description="Server error")
     * )
     * @return ApiSuccessResponse
     * @throws CustomException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function export(): ApiSuccessResponse
    {
        try {
            $export =  new MyTeamExport($this->service);
            $fileName = '/my-teams/Команды-' . date('Y-m-d_H-i-s') . '.xlsx';
            Excel::store($export, $fileName);
            $link = storage_path('/app/' . $fileName);
            return new ApiSuccessResponse($link, ['message' => 'Ok'], Response::HTTP_OK);
        } catch (Exception|Exception $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
