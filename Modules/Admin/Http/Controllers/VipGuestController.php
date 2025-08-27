<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\VipRequest\ExportVipGuestRequest;
use Admin\Http\Requests\VipRequest\ListVipGuestRequest;
use Admin\Http\Requests\VipRequest\ShowVipGuestRequest;
use Admin\Http\Resources\VipGuest\VipGuestCollection;
use Admin\Http\Resources\VipGuest\VipGuestResource;
use Admin\Services\CommonService;
use Admin\Services\VipGuestService;
use App\Exceptions\CustomException;
use App\Exports\VipGuestExport;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Maatwebsite\Excel\Facades\Excel;
use OpenApi\Annotations as OA;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class VipGuestController extends Controller
{

    public function __construct(public VipGuestService $vipGuestService, public CommonService $commonService)
    {
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/vip-guest/all",
     *      tags={"Admin|VIP-гости"},
     *      security={{"bearerAuth":{}}},
     *      summary="Список всех VIP-гостей",
     *      operationId="AdminVipGuests",
     *      @OA\Parameter(
     *          name="filter[id]",
     *          in="query",
     *          description="Фильтр по ID",
     *          @OA\Schema(
     *              type="int",
     *          )
     *      ),
     *      @OA\Parameter(
     *         name="filter[user_application_id]",
     *         in="query",
     *         description="Фильтр по ID заявки",
     *         @OA\Schema(
     *             type="integer",
     *          )
     *       ),
     *      @OA\Parameter(
     *          name="filter[user_id]",
     *          in="query",
     *          description="Фильтр по ID Учапстника",
     *          @OA\Schema(
     *              type="integer",
     *           )
     *        ),
     *       @OA\Parameter(
     *            name="sort",
     *            in="query",
     *            description="Сортировка по поле",
     *            @OA\Schema(
     *               type="string",
     *               enum={"id","-id","user_application_id","-user_application_id","organization->ru","-organization->ru",
     *                    "email","-email","created_at","-created_at","updated_at","-updated_at","full_name->ru","-full_name->ru"
     *                    },
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
     *      @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            @OA\Property(
     *              property="data",
     *              description="ID гостя",
     *              type="object",
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="vip-guest", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="1"),
     *                 @OA\Property(property="user_application_id", description="Идентификатор заявки", type="integer", example="1"),
     *                 @OA\Property(property="full_name", description="ФИО чудожника", type="object",
     *                      @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                      @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *                  ),
     *                  @OA\Property(property="organization", description="Организация", type="object",
     *                      @OA\Property(property="ru", example="МГУ"),
     *                      @OA\Property(property="en", example="MDU")
     *                  ),
     *                 @OA\Property(property="email", type="sting", example="john.doe@example.com"),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="user_profile", description="Профиль запольнителя", type="object",
     *                       @OA\Property(property="id", example="1", type="integer"),
     *                       @OA\Property(property="name", description="Имя", type="object",
     *                          @OA\Property(property="ru", example="Иван"),
     *                          @OA\Property(property="en", example="Ivan")
     *                        ),
     *                       @OA\Property(property="surname", description="Фамилия", type="object",
     *                           @OA\Property(property="ru", example="Иванов"),
     *                           @OA\Property(property="en", example="Ivaov")
     *                         ),
     *                       @OA\Property(property="laravel_through_key", type="integer", example=1),
     *                    ),
     *                 @OA\Property(property="user_application", description="Заявка", type="object",
     *                        @OA\Property(property="id", example="1", type="integer"),
     *                        @OA\Property(property="user_id", example="1", type="integer"),
     *                        @OA\Property(property="representative_nam", description="Имя", type="object",
     *                           @OA\Property(property="ru", example="Иван"),
     *                           @OA\Property(property="en", example="Ivan")
     *                         ),
     *                        @OA\Property(property="representative_surname", description="Фамилия", type="object",
     *                            @OA\Property(property="ru", example="Иванов"),
     *                            @OA\Property(property="en", example="Ivaov")
     *                          ),
     *                        @OA\Property(property="laravel_through_key", type="integer", example=1),
     *                     ),
     *              ),
     *               @OA\Property(property="links", type="object",
     *                    @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/vip-guest/2"),
     *                 ),
     *                @OA\Property(property="relationships", type="object",
     *                   @OA\Property(property="events", type="object",
     *                     @OA\Property(property="data",type="object"),
     *                     @OA\Property(property="links",type="object",
     *                        @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/vip-guest/2/relationships/userApplications"),
     *                        @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/vip-guest/2/userApplications" ),
     *                    ),
     *                   ),
     *                 ),
     *               ),
     *                 @OA\Property(property="metadata",type="object"),
     *               ),
     *              ),
     *            ),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404,description="Not Found"),
     *            @OA\Response(response=403,description="Forbidden",
     *           @OA\JsonContent(
     *               @OA\Property(property="errors", type="array",
     *                  @OA\Items(
     *                  @OA\Property(property="status", example="403"),
     *                  @OA\Property(property="detail", example="User does not have the right roles.")
     *                  ),
     *              ),
     *           ),
     *       ),
     * )
     * @param ListVipGuestRequest $request
     * @return VipGuestCollection|ApiErrorResponse
     */
    public function list(ListVipGuestRequest $request): VipGuestCollection|ApiErrorResponse
    {
        try {
            $dataApp = $request->validated();
            return new VipGuestCollection($this->vipGuestService->list($dataApp));
        } catch (Throwable $e) {
            return new ApiErrorResponse($e->getMessage(), null, ResponseAlias::HTTP_FORBIDDEN);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/vip-guest/{id}",
     *      tags={"Admin|VIP-гости"},
     *      security={{"bearerAuth":{}}},
     *      summary="Получать VIP-гося",
     *      operationId="AdminVipSowGuests",
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          required=true,
     *          @OA\Schema(
     *             type="integer",
     *             example=1,
     *          )
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
     *               description="ID гостя",
     *               type="object",
     *               @OA\Property(property="id", example="1", type="integer"),
     *               @OA\Property(property="type", example="vip-guest", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *                  @OA\Property(property="id", description="Идентификатор", type="integer", example="1"),
     *                  @OA\Property(property="user_application_id", description="Идентификатор заявки", type="integer", example="1"),
     *                  @OA\Property(property="full_name", description="ФИО чудожника", type="object",
     *                       @OA\Property(property="ru", example="Иванов Иван Василевич"),
     *                       @OA\Property(property="en", example="Ivanov Ivan Ivanovich")
     *                   ),
     *                   @OA\Property(property="organization", description="Организация", type="object",
     *                       @OA\Property(property="ru", example="МГУ"),
     *                       @OA\Property(property="en", example="MDU")
     *                   ),
     *                  @OA\Property(property="email", type="sting", example="john.doe@example.com"),
     *                  @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                  @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                  @OA\Property(property="user_profile", description="Профиль запольнителя", type="object",
     *                        @OA\Property(property="id", example="1", type="integer"),
     *                        @OA\Property(property="name", description="Имя", type="object",
     *                           @OA\Property(property="ru", example="Иван"),
     *                           @OA\Property(property="en", example="Ivan")
     *                         ),
     *                        @OA\Property(property="surname", description="Фамилия", type="object",
     *                            @OA\Property(property="ru", example="Иванов"),
     *                            @OA\Property(property="en", example="Ivaov")
     *                          ),
     *                        @OA\Property(property="laravel_through_key", type="integer", example=1),
     *                     ),
     *                  @OA\Property(property="user_application", description="Заявка", type="object",
     *                         @OA\Property(property="id", example="1", type="integer"),
     *                         @OA\Property(property="user_id", example="1", type="integer"),
     *                         @OA\Property(property="representative_nam", description="Имя", type="object",
     *                            @OA\Property(property="ru", example="Иван"),
     *                            @OA\Property(property="en", example="Ivan")
     *                          ),
     *                         @OA\Property(property="representative_surname", description="Фамилия", type="object",
     *                             @OA\Property(property="ru", example="Иванов"),
     *                             @OA\Property(property="en", example="Ivaov")
     *                           ),
     *                         @OA\Property(property="laravel_through_key", type="integer", example=1),
     *                      ),
     *               ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/vip-guest/2"),
     *                ),
     *               @OA\Property(property="relationships", type="object",
     *                  @OA\Property(property="events", type="object",
     *                    @OA\Property(property="data",type="object"),
     *                    @OA\Property(property="links",type="object",
     *                       @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/vip-guest/2/relationships/userApplications"),
     *                       @OA\Property(property="related",type="string",example="http://newapiartrussiafair/api/v1/admin/vip-guest/2/userApplications" ),
     *                   ),
     *                  ),
     *                ),
     *              ),
     *                  @OA\Property(property="metadata",type="object",
     *                    @OA\Property(property="message", type="string", example="Ok"),
     *                   ),
     *                ),
     *               ),
     *             ),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404,description="Not Found",
     *         @OA\JsonContent(
     *              @OA\Property(property="errors", type="array",
     *                @OA\Items(
     *                   @OA\Property(property="status", type="integer", example=404),
     *                   @OA\Property(property="detail", example="Ресурс с идентификатором 14 не был найден")
     *                )
     *             ),
     *          ),
     *     ),
     *      @OA\Response(response=403,description="Forbidden",
     *          @OA\JsonContent(
     *              @OA\Property(property="errors", type="array",
     *                 @OA\Items(
     *                 @OA\Property(property="status", type="integer", example=403),
     *                 @OA\Property(property="detail", example="User does not have the right roles.")
     *                 ),
     *             ),
     *          ),
     *      ),
     * )
     * @param ShowVipGuestRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function show(ShowVipGuestRequest $request): ApiSuccessResponse
    {
        $appData = $request->validated();
        return new ApiSuccessResponse(
            new VipGuestResource($this->vipGuestService->show($appData['id'])),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/vip-guest/export",
     *      operationId="exportVipGuests",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|VIP-гости"},
     *      summary="Экспорт списка VIP гостей",
     *      @OA\Parameter(
     *           name="filter[id]",
     *           in="query",
     *           description="Фильтр по ID",
     *           @OA\Schema(
     *               type="int",
     *           )
     *       ),
     *       @OA\Parameter(
     *          name="filter[user_application_id]",
     *          in="query",
     *          description="Фильтр по ID заявки",
     *          @OA\Schema(
     *              type="integer",
     *           )
     *        ),
     *       @OA\Parameter(
     *           name="filter[user_id]",
     *           in="query",
     *           description="Фильтр по ID Учапстника",
     *           @OA\Schema(
     *               type="integer",
     *            )
     *         ),
     *        @OA\Parameter(
     *             name="sort",
     *             in="query",
     *             description="Сортировка по поле",
     *             @OA\Schema(
     *                type="string",
     *                enum={"id","-id","user_application_id","-user_application_id","organization->ru","-organization->ru",
     *                     "email","-email","created_at","-created_at","updated_at","-updated_at","full_name->ru","-full_name->ru"
     *                     },
     *              )
     *          ),
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
     *                  example="C:\\xampp\\htdocs\\newapiartrussiafair\\storage\\/app//vip-guests/vip-guest-1701946992.xlsx"
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
     * @param ExportVipGuestRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function export(ExportVipGuestRequest $request): ApiSuccessResponse
    {
        try {
            $appData = $request->validated();
            $export =  new VipGuestExport($appData, $this->vipGuestService);
            $fileName = '/vip-guests/Гости-' . date('Y-m-d_H-i-s') . '.xlsx';
            Excel::store($export, $fileName);
            $link = storage_path('/app/' . $fileName);
            return new ApiSuccessResponse($link, ['message' => 'Ok'], ResponseAlias::HTTP_OK);
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception|Exception $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }
}
