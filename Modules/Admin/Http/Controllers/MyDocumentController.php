<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\MyDocument\MyDocumentDeleteRequest;
use Admin\Http\Requests\MyDocument\MyDocumentListRequest;
use Admin\Http\Requests\MyDocument\MyDocumentUploadRequest;
use Admin\Http\Resources\Artist\ArtistCollection;
use Admin\Http\Resources\MyDocuments\MyDocumentCollection;
use Admin\Services\MyDocumentService;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Http\JsonResponse;
use Lk\Http\Requests\MyDocuments\MyDocumentShowRequest;
use Lk\Http\Resources\MyDocuments\MyDocumentResource;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class MyDocumentController extends Controller
{
    public function __construct(protected MyDocumentService $documentService)
    {
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/my-documents/agreements",
     *    operationId="AdminmyDocumentAgreementFiles",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Мои документы"},
     *    summary="Список шаблонов соглашения",
     *    @OA\Response(
     *       response=200,
     *       description="Success",
     *       @OA\MediaType(
     *       mediaType="application/vnd.api+json",
     *       @OA\Schema(
     *          type="object",
     *          @OA\Property(property="data", type="array",
     *             @OA\Items(
     *                @OA\Property(property="name", type="string", example="some_doc.pdf"),
     *                @OA\Property(property="url", type="string", example="C:\\xampp\\htdocs\\newapiartrussiafair\\storage\\/app/agreement/some_doc.pdf"),
     *               ),
     *             ),
     *           ),
     *        )
     *     ),
     *    @OA\Response(response=401, description="Unauthenticated"),
     *    @OA\Response(response=400,description="Bad Request"),
     *    @OA\Response(response=404, description="Not Found"),
     *    @OA\Response(response=403,description="Forbidden",
     *       @OA\JsonContent(
     *           @OA\Property(property="errors", type="array",
     *              @OA\Items(
     *                 @OA\Property(property="status", example="403"),
     *                 @OA\Property(property="detail", example="User does not have the right roles.")
     *              ),
     *           ),
     *         ),
     *      ),
     * )
     * @return JsonResponse
     * @throws CustomException
     */
    public function agreements(): JsonResponse
    {
        $documents = $this->documentService->getFiles();
        return response()->json([
            'data' => $documents
        ]);
    }

    /**
     * @OA\Post(
     *    path="/api/v1/admin/my-documents/agreement/upload",
     *    operationId="AdminMyDocumentStor",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Мои документы"},
     *    summary="Загрузка соглашения",
     *    @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *              @OA\Property(
     *                 description="file to upload",
     *                 property="file",
     *                 type="string",
     *                ),
     *             @OA\Property(property="name", type="string", example="some_doc.pdf"),
     *             )
     *          )
     *      ),
     *    @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\MediaType(
     *       mediaType="application/vnd.api+json",
     *       @OA\Schema(
     *              type="object",
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="url", type="string", example="C:\\xampp\\htdocs\\newapiartrussiafair\\storage\\/app/agreement/some_doc.pdf"),
     *                  ),
     *              ),
     *          ),
     *       )
     *    ),
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
     * @param MyDocumentUploadRequest $request
     * @return JsonResponse
     * @throws CustomException
     */
    public function upload(MyDocumentUploadRequest $request): JsonResponse
    {
        $dataApp = $request->validated();
        $documents = $this->documentService->upload($dataApp['file'], $dataApp['name']);
        return response()->json([
            'data' => [
                $documents
            ],
        ]);
    }

    /**
     * @OA\Delete(
     *    path="/api/v1/admin/my-documents/agreement/delete",
     *    operationId="AdminMyDocumentDelete",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Мои документы"},
     *    summary="Удаление документа по название",
     *    @OA\Parameter(
     *        name="name",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *             type="string",
     *             example="agreement"
     *        )
     *       ),
     *    @OA\Response(
     *       response=200,
     *       description="Success",
     *       @OA\MediaType(
     *       mediaType="application/vnd.api+json",
     *       @OA\Schema(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Файл успешно удален."),
     *           ),
     *         ),
     *       ),
     *    ),
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
     * @param MyDocumentDeleteRequest $request
     * @return JsonResponse
     * @throws CustomException
     */
    public function delete(MyDocumentDeleteRequest $request): JsonResponse
    {
        $dataApp = $request->validated();
        $documents = $this->documentService->delete($dataApp['name']);
        return response()->json([
            'data' => [
                'status'  => $documents,
                'message' => 'Файл успешно удален.'
            ],
        ]);
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/my-documents/show",
     *    operationId="AdminmyDocumentShow",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Мои документы"},
     *    summary="Просмотр документов",
     *    @OA\Parameter(
     *       name="user_application_id",
     *       in="query",
     *       required=true,
     *       @OA\Schema(
     *            type="string",
     *            example="1"
     *       )
     *      ),
     *      @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *               property="data",
     *               description="ID художника",
     *               type="object",
     *               @OA\Property(property="id", example="1", type="integer"),
     *               @OA\Property(property="type", example="my-documents", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *                  @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                  @OA\Property(property="user_application_id",description="Идентификатор заявки",type="integer",example="2"),
     *                  @OA\Property(property="status", description="Статус", type="string", example="self-employed"),
     *                  @OA\Property(property="files", description="Сканы", type="array",
     *                     @OA\Items(
     *                       @OA\Property(property="name", example="passport_"),
     *                       @OA\Property(property="type", example="passport"),
     *                       @OA\Property(property="url", example="/uploads/my_documents/SxOUlRCEL27ipFjfJJ85.png"),
     *                     ),
     *                  ),
     *                  @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                  @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                  @OA\Property(property="contacts",description="Контакты",type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="my_document_id", type="integer", example=1),
     *                          @OA\Property(property="phone", type="string", example="89264558454"),
     *                          @OA\Property(property="email", type="string", example="test@mail.ru"),
     *                          @OA\Property(property="edo_operator", type="string", example="test"),
     *                          @OA\Property(property="edo_id", type="string", example="test id"),
     *                      ),
     *                    ),
     *                  @OA\Property(property="requisites",description="Контакты",type="array",
     *                       @OA\Items(
     *                           @OA\Property(property="id", type="integer", example=1),
     *                           @OA\Property(property="my_document_id", type="integer", example=1),
     *                           @OA\Property(property="payment_account", type="string", example="323242342342342344"),
     *                           @OA\Property(property="bank_nam", type="string", example="Сбер"),
     *                           @OA\Property(property="bic", type="string", example="2321312312321"),
     *                           @OA\Property(property="correspondent_account", type="string", example="2321312312321"),
     *                           @OA\Property(property="kpp", type="string", example="2321312312321"),
     *                           @OA\Property(property="inn", type="string", example="2321312312321"),
     *                       )
     *                   ),
     *                ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/artist/8"),
     *                ),
     *               @OA\Property(property="relationships", type="object",
     *                  @OA\Property(property="contacts", type="object",
     *                    @OA\Property(property="data",type="object"),
     *                  ),
     *               @OA\Property(property="requisites", type="object",
     *                     @OA\Property(property="data",type="object"),
     *                   ),
     *                ),
     *              ),
     *               @OA\Property(property="metadata",type="object",
     *                     @OA\Property(property="message", example="Ok"),
     *                   ),
     *                ),
     *               ),
     *             ),
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
     * @param MyDocumentShowRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function show(MyDocumentShowRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $dataApp = $request->validated();
            $documents = $this->documentService->show($dataApp['user_application_id']);
            return new ApiSuccessResponse(
                new MyDocumentResource($documents),
                ['message' => 'Ок'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при получение документов', $e);
        }
    }

    /**
     * @OA\Get(
     *       path="/api/v1/admin/my-documents/list",
     *       operationId="AdminAdminList",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Мои документы"},
     *       summary="Получить список документов",
     *       @OA\Parameter(
     *          name="filter[id]",
     *          in="query",
     *          description="ID Документа",
     *          @OA\Schema(
     *             type="integer",
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="filter[user_application_id]",
     *          in="query",
     *          description="ID заявки",
     *          @OA\Schema(
     *             type="integer",
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="filter[status]",
     *         in="query",
     *         description="Статус",
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
     *            enum={"id","status","user_application_id","created_at", "updated_at"},
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
     *       @OA\Parameter(
     *             name="page",
     *             in="query",
     *             description="Номер страницы",
     *             @OA\Schema(
     *                type="integer",
     *                example=1
     *            )
     *        ),
     *       @OA\Parameter(
     *           name="per_page",
     *           in="query",
     *           description="Количество элементов на странице",
     *           @OA\Schema(
     *              type="integer",
     *               example=10
     *            )
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
     *               description="ID художника",
     *               type="array",
     *               @OA\Items(
     *               @OA\Property(property="id", example="1", type="integer"),
     *               @OA\Property(property="type", example="my-documents", type="string"),
     *                  @OA\Property(property="attributes", type="object",
     *                  @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                  @OA\Property(property="user_application_id",description="Идентификатор заявки",type="integer",example="2"),
     *                  @OA\Property(property="status", description="Статус", type="string", example="self-employed"),
     *                  @OA\Property(property="files", description="Сканы", type="array",
     *                     @OA\Items(
     *                       @OA\Property(property="name", example="passport_"),
     *                       @OA\Property(property="type", example="passport"),
     *                       @OA\Property(property="url", example="/uploads/my_documents/SxOUlRCEL27ipFjfJJ85.png"),
     *                     ),
     *                  ),
     *                  @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                  @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                  @OA\Property(property="contacts",description="Контакты",type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="my_document_id", type="integer", example=1),
     *                          @OA\Property(property="phone", type="string", example="89264558454"),
     *                          @OA\Property(property="email", type="string", example="test@mail.ru"),
     *                          @OA\Property(property="edo_operator", type="string", example="test"),
     *                          @OA\Property(property="edo_id", type="string", example="test id"),
     *                      ),
     *                    ),
     *                  @OA\Property(property="requisites",description="Контакты",type="array",
     *                       @OA\Items(
     *                           @OA\Property(property="id", type="integer", example=1),
     *                           @OA\Property(property="my_document_id", type="integer", example=1),
     *                           @OA\Property(property="payment_account", type="string", example="323242342342342344"),
     *                           @OA\Property(property="bank_nam", type="string", example="Сбер"),
     *                           @OA\Property(property="bic", type="string", example="2321312312321"),
     *                           @OA\Property(property="correspondent_account", type="string", example="2321312312321"),
     *                           @OA\Property(property="kpp", type="string", example="2321312312321"),
     *                           @OA\Property(property="inn", type="string", example="2321312312321"),
     *                       )
     *                   ),
     *                 ),
     *               ),
     *               @OA\Property(property="links", type="object",
     *                   @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/artist/8"),
     *                ),
     *               @OA\Property(property="relationships", type="object",
     *                      @OA\Property(property="contacts", type="object",
     *                      @OA\Property(property="data",type="object"),
     *                    ),
     *                      @OA\Property(property="requisites", type="object",
     *                      @OA\Property(property="data",type="object"),
     *                     ),
     *                ),
     *              ),
     *                  @OA\Property(property="metadata",type="object",
     *                     @OA\Property(property="message", example="Документы успешно добавлен"),
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
     * @param MyDocumentListRequest $request
     * @return ArtistCollection|ApiErrorResponse
     */
    public function list(MyDocumentListRequest $request): MyDocumentCollection|ApiErrorResponse
    {
        try {
            $appData = $request->validated();
            return new MyDocumentCollection($this->documentService->list($appData));
        } catch (NotFoundHttpException $e) {
            return new ApiErrorResponse('Ошибка при выводе списока документов', $e);
        }
    }
}
