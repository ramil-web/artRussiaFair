<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\Person\ExportPersonRequest;
use Admin\Http\Requests\Person\ListPersonRequest;
use Admin\Http\Resources\Employee\EmployeeCollection;
use Admin\Http\Resources\Employee\EmployeeResource;
use Admin\Services\CommonService;
use Admin\Services\PersonService;
use App\Enums\PersonTypesEnum;
use App\Exceptions\CustomException;
use App\Exports\EmployeeExport;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Maatwebsite\Excel\Facades\Excel;
use OpenApi\Annotations as OA;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class EmployeeController extends Controller
{

    private PersonTypesEnum $type;

    public function __construct(public PersonService $personService, public CommonService $commonService)
    {
        $this->type = PersonTypesEnum::EMPLOYEE();
    }

    /**
     * @OA\Get(
     *       path="/api/v1/admin/employee/list",
     *       tags={"Admin|Сотрудники"},
     *       security={{"bearerAuth":{}}},
     *       summary="Список сотрудников",
     *       operationId="AdminGetEmployees",
     *       @OA\Parameter(
     *           name="filter[id]",
     *           in="query",
     *           description="Фильтр по id",
     *           @OA\Schema(
     *               type="string",
     *           )
     *       ),
     *       @OA\Parameter(
     *          name="filter[user_application_id]",
     *          in="query",
     *          description="Фильтр по id Заявки",
     *          @OA\Schema(
     *                type="string",
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\MediaType(
     *             mediaType="application/vnd.api+json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                     @OA\Property(property="id", type="integer", example="1"),
     *                     @OA\Property(property="employee", type="string", example="employee"),
     *                     @OA\Property(
     *                        property="attributes",
     *                        type="object",
     *                        @OA\Property(property="id", type="integer", example="1"),
     *                        @OA\Property(property="user_application_id", type="integer", example="1"),
     *                        @OA\Property(property="full_name", type="object",
     *                           @OA\Property(property="ru", type="string", example="John Doe"),
     *                        ),
     *                        @OA\Property(property="passport", type="sting", example="240675847"),
     *                        @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                        @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                     ),
     *                     @OA\Property(
     *                        property="links",
     *                        type="object",
     *                        @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/lk/employee/1"),
     *                     ),
     *                     @OA\Property(property="relationships",type="object",
     *                        @OA\Property(property="user_applications",type="object"),
     *                     ),
     *                 ),
     *                ),
     *                 @OA\Property(property="links", type="object",
     *                 @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/lk/employee/list"),
     *                ),
     *             ),
     *          ),
     *        ),
     *       @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=400,description="Bad Request"),
     *       @OA\Response(response=404,description="Not Found"),
     *       @OA\Response(response=403,description="Forbidden",
     *           @OA\JsonContent(
     *               @OA\Property(property="errors", type="array",
     *               @OA\Items(
     *                   @OA\Property(property="status", example="403"),
     *                   @OA\Property(property="detail", example="User does not have the right roles.")
     *               ),
     *            ),
     *         ),
     *      ),
     *  )
     * @param ListPersonRequest $request
     * @return ApiErrorResponse|EmployeeCollection
     */
    public function list(ListPersonRequest $request): ApiErrorResponse|EmployeeCollection
    {
        try {
            $dataApp = $request->validated();
            return new EmployeeCollection($this->personService->list($dataApp, $this->type));
        } catch (Throwable $e) {
            return new ApiErrorResponse($e->getMessage(), null, ResponseAlias::HTTP_FORBIDDEN);
        }
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/employee/{id}",
     *    tags={"Admin|Сотрудники"},
     *    security={{"bearerAuth":{}}},
     *    summary="Просмотр сотрудника",
     *    operationId="AdminGetEmployee",
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
     *                  @OA\Property(property="employee", type="string", example="employee"),
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
     *                     @OA\Property(property="self", type="string", example="http://newapiartrussiafair/api/v1/lk/employee/1")
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
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function show(int $id): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            new EmployeeResource($this->personService->show($id, $this->type)),
            ['message' => 'Ok'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/employee/export",
     *      operationId="exportEmployees",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Сотрудники"},
     *      summary="Экспорт списка сотрудников",
     *      @OA\Parameter(
     *          name="filter[from]",
     *          in="query",
     *          description="Начальная дата",
     *          @OA\Schema(
     *             type="sting",
     *             example="2023-11-27 15:15"
     *          )
     *       ),
     *       @OA\Parameter(
     *           name="filter[to]",
     *           in="query",
     *           description="Конечная дата",
     *           @OA\Schema(
     *              type="string",
     *              example="2023-12-27 15:15"
     *           )
     *        ),     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *              @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                  property="data",
     *                  type="string",
     *                  example="C:\\xampp\\htdocs\\newapiartrussiafair\\storage\\/app//employees/employees-2023-12-14 07:56:25.xlsx"
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
     * @param ExportPersonRequest $request
     * @return ApiSuccessResponse
     * @throws CustomException
     */
    public function export(ExportPersonRequest $request): ApiSuccessResponse
    {
        try {
            $fileName = '/employees/employees-' .date('Y-m-d_H-i-s'). '.xlsx';
            Excel::store(
                new EmployeeExport($this->commonService->dateInterval($request->validated())),
                $fileName
            );
            $link = storage_path('/app/'.$fileName);
            return new ApiSuccessResponse($link, ['message' => 'Ok'], ResponseAlias::HTTP_OK);
        } catch (Exception|\PhpOffice\PhpSpreadsheet\Exception $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }

    }
}
