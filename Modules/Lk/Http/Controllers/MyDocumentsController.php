<?php

namespace Lk\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Lk\Http\Requests\MyDocuments\MyDocumentsDeleteFileRequest;
use Lk\Http\Requests\MyDocuments\MyDocumentShowRequest;
use Lk\Http\Requests\MyDocuments\MyDocumentStoreRequest;
use Lk\Http\Requests\MyDocuments\MyDocumentUpdateRequest;
use Lk\Http\Resources\MyDocuments\MyDocumentResource;
use Lk\Services\MyDocumentsService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class MyDocumentsController extends Controller
{
    public function __construct(protected MyDocumentsService $documentsService)
    {
    }

    /**
     * @OA\Post(
     *    path="/api/v1/lk/my-documents/store",
     *    operationId="Lk.myDocumentStor",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Мои документы"},
     *    summary="Добавление документов",
     *    @OA\Parameter(
     *       name="status",
     *       in="query",
     *       required=true,
     *       @OA\Schema(
     *            type="string",
     *            enum={"individual","self-employed","legal_entity","sole_entrepreneur"}
     *       )
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *             type="object",
     *             required={"user_application_id","files","phone","email"},
     *             @OA\Property(property="user_application_id",description="ID заявки",type="integer",example=21),
     *             @OA\Property(property="payment_account",description="Расчетю счет",type="string",example="891632323231728569"),
     *             @OA\Property(property="bank_name",description="Наименование банка",type="string",example="Сбер"),
     *             @OA\Property(property="bic",type="string",description="БИК", example="21212121sds"),
     *             @OA\Property(property="correspondent_account",description="Корс. счет",type="string",example="2121212323232323232"),
     *             @OA\Property(property="kpp",description="КПП",type="string",example="2121212323232323232"),
     *             @OA\Property(property="inn",description="ИНН",type="string",example="26212323232323232"),
     *             @OA\Property(property="phone",description="Телефон",type="string",example="89161728569"),
     *             @OA\Property(property="email",description="Эл. почта",type="string",example="test@mail.ru"),
     *             @OA\Property(property="edo_operator",description="Оператор ЭДО",type="string",example="Some text"),
     *             @OA\Property(property="edo_id",description="Идентификатор ЭДО",type="string",example="1dsd2323232"),
     *             @OA\Property(property="files",description="Сканы файлов",type="array",
     *                 example={
     *                          {"name":"passport_1","type":"passport","url":"/uploads/my_documents/SxOUlRCEL27ipFjfJJ85.png"},
     *                          {"name":"passport_2","type":"passport","url":"/uploads/my_documents/viwt6arbf25RsXfJ4QrU.png"},
     *                        },
     *                @OA\Items(),
     *                ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *            type="object",
     *            @OA\Property(
     *              property="data",
     *              description="ID художника",
     *              type="object",
     *              @OA\Property(property="id", example="1", type="integer"),
     *              @OA\Property(property="type", example="my-documents", type="string"),
     *                 @OA\Property(property="attributes", type="object",
     *                 @OA\Property(property="id", description="Идентификатор", type="integer", example="2"),
     *                 @OA\Property(property="user_application_id",description="Идентификатор заявки",type="integer",example="2"),
     *                 @OA\Property(property="status", description="Статус", type="string", example="self-employed"),
     *                 @OA\Property(property="files", description="Сканы", type="array",
     *                    @OA\Items(
     *                      @OA\Property(property="name", example="passport_"),
     *                      @OA\Property(property="type", example="passport"),
     *                      @OA\Property(property="url", example="/uploads/my_documents/SxOUlRCEL27ipFjfJJ85.png"),
     *                    ),
     *                 ),
     *                 @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                 @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                 @OA\Property(property="contacts",description="Контакты",type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="my_document_id", type="integer", example=1),
     *                         @OA\Property(property="phone", type="string", example="89264558454"),
     *                         @OA\Property(property="email", type="string", example="test@mail.ru"),
     *                         @OA\Property(property="edo_operator", type="string", example="test"),
     *                         @OA\Property(property="edo_id", type="string", example="test id"),
     *                     ),
     *                   ),
     *                 @OA\Property(property="requisites",description="Контакты",type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="my_document_id", type="integer", example=1),
     *                          @OA\Property(property="payment_account", type="string", example="323242342342342344"),
     *                          @OA\Property(property="bank_nam", type="string", example="Сбер"),
     *                          @OA\Property(property="bic", type="string", example="2321312312321"),
     *                          @OA\Property(property="correspondent_account", type="string", example="2321312312321"),
     *                          @OA\Property(property="kpp", type="string", example="2321312312321"),
     *                          @OA\Property(property="inn", type="string", example="2321312312321"),
     *                      )
     *                  ),
     *               ),
     *              @OA\Property(property="links", type="object",
     *                  @OA\Property(property="self",type="string", example="http://newapiartrussiafair/api/v1/admin/artist/8"),
     *               ),
     *              @OA\Property(property="relationships", type="object",
     *                     @OA\Property(property="contacts", type="object",
     *                     @OA\Property(property="data",type="object"),
     *                   ),
     *                     @OA\Property(property="requisites", type="object",
     *                     @OA\Property(property="data",type="object"),
     *                    ),
     *               ),
     *             ),
     *                 @OA\Property(property="metadata",type="object",
     *                    @OA\Property(property="message", example="Документы успешно добавлен"),
     *                  ),
     *               ),
     *              ),
     *            ),
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
     * @param MyDocumentStoreRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(MyDocumentStoreRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $dataApp = $request->validated();

            /**
             * Проверяем ID заяки пренодложить авторизованныму участнику или нет
             */
            $this->documentsService->checkUserApp($dataApp['user_application_id']);

            $documents = $this->documentsService->create($dataApp);
            return new ApiSuccessResponse(
                new MyDocumentResource($documents),
                ['message' => 'Документы успешно добавлены'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при добавлении документов', $e);
        }
    }

    /**
     * @OA\Patch(
     *    path="/api/v1/lk/my-documents/update",
     *    operationId="Lk.myDocumentUpdate",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Мои документы"},
     *    summary="Редактирование документов",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
     *       @OA\Schema(
     *            type="string",
     *            example="1"
     *       )
     *      ),
     *    @OA\Parameter(
     *        name="status",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *             type="string",
     *             enum={"individual","self-employed","legal_entity","sole_entrepreneur"}
     *        )
     *       ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *          mediaType="application/vnd.api+json",
     *          @OA\Schema(
     *             type="object",
     *             required={"id"},
     *             @OA\Property(property="user_application_id",description="ID заявки",type="integer",example=21),
     *             @OA\Property(property="payment_account",description="Расчетю счет",type="string",example="891632323231728569"),
     *             @OA\Property(property="bank_name",description="Наименование банка",type="string",example="Сбер"),
     *             @OA\Property(property="bic",type="string",description="БИК", example="21212121sds"),
     *             @OA\Property(property="correspondent_account",description="Корс. счет",type="string",example="2121212323232323232"),
     *             @OA\Property(property="kpp",description="КПП",type="string",example="2121212323232323232"),
     *             @OA\Property(property="inn",description="ИНН",type="string",example="26212323232323232"),
     *             @OA\Property(property="phone",description="Телефон",type="string",example="89161728569"),
     *             @OA\Property(property="email",description="Эл. почта",type="string",example="test@mail.ru"),
     *             @OA\Property(property="edo_operator",description="Оператор ЭДО",type="string",example="Some text"),
     *             @OA\Property(property="edo_id",description="Идентификатор ЭДО",type="string",example="1dsd2323232"),
     *             @OA\Property(property="files",description="Сканы файлов",type="array",
     *                 example={
     *                          {"name":"passport_1","type":"passport","url":"/uploads/my_documents/SxOUlRCEL27ipFjfJJ85.png"},
     *                          {"name":"passport_2","type":"passport","url":"/uploads/my_documents/viwt6arbf25RsXfJ4QrU.png"},
     *                        },
     *                @OA\Items(),
     *                ),
     *              ),
     *          ),
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
     *                      @OA\Property(property="contacts", type="object",
     *                      @OA\Property(property="data",type="object"),
     *                    ),
     *                      @OA\Property(property="requisites", type="object",
     *                      @OA\Property(property="data",type="object"),
     *                     ),
     *                ),
     *              ),
     *                  @OA\Property(property="metadata",type="object",
     *                     @OA\Property(property="message", example="Документы успешно обновлен"),
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
     * @param MyDocumentUpdateRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(MyDocumentUpdateRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $dataApp = $request->validated();

            $documents = $this->documentsService->update($dataApp);
            return new ApiSuccessResponse(
                new MyDocumentResource($documents),
                ['message' => 'Документы успешно обновлен'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при редактирование документов', $e);
        }
    }

    /**
     * @OA\Get(
     *    path="/api/v1/lk/my-documents/show",
     *    operationId="Lk.myDocumentShow",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Мои документы"},
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

            /**
             * Проверяем ID заяки пренодложить авторизованныму участнику или нет
             */
            if (!$this->documentsService->checkUserAppIsOwn($dataApp['user_application_id'])) {
                return new ApiSuccessResponse(
                    [],
                    ['message' => 'Ок'],
                    ResponseAlias::HTTP_OK
                );
            }

            $documents = $this->documentsService->show($dataApp['user_application_id']);
            $response = !empty($documents) ? new MyDocumentResource($documents) : $documents;
            return new ApiSuccessResponse(
                $response,
                ['message' => 'Ок'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при получение документов', $e);
        }
    }

    /**
     * @OA\Patch(
     *    path="/api/v1/lk/my-documents/delete-file",
     *    operationId="Lk.myDocumentDeleteFile",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Мои документы"},
     *    summary="Удаление файла",
     *    @OA\Parameter(
     *       name="id",
     *       in="query",
     *       required=true,
     *       @OA\Schema(
     *            type="string",
     *            example="1"
     *       )
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *           @OA\Schema(
     *              type="object",
     *              required={"id"},
     *              @OA\Property(property="url",description="Путь к файлу",type="string",example="/uploads/my_documents/SxOUlRCEL27ipFjfJJ85.png"),
     *              @OA\Property(property="type",description="Тип файла",type="string",example="passport"),
     *              @OA\Property(property="name",description="Название файла",type="string",example="passport_1"),
     *              ),
     *           ),
     *       ),
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
     * @param MyDocumentsDeleteFileRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function deleteFile(MyDocumentsDeleteFileRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $dataApp = $request->validated();
            $documents = $this->documentsService->deleteFile($dataApp);
            return new ApiSuccessResponse(
                $documents,
                ['message' => 'Файл успешно удален'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при удоление файла', $e);
        }
    }

    /**
     * @OA\Get(
     *    path="/api/v1/lk/my-documents/agreement",
     *    operationId="Lk.myDocumentAgreement",
     *    security={{"bearerAuth":{}}},
     *    tags={"Lk|Мои документы"},
     *    summary="Выгрузка шаблона соглашения",
     *    @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *              type="object",
     *              @OA\Property(property="data", type="array",
     *                  example={"C:\\xampp\\htdocs\\newapiartrussiafair\\storage\\/app/agreement/some_doc.pdf"},
     *                  @OA\Items(type="string"),
     *            ),
     *            @OA\Property(property="metadata",type="object",
     *               @OA\Property(property="message", example="Ok"),
     *            ),
     *          ),
     *        ),
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
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function agreementFile(): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $link = $this->documentsService->getAgreementFile();
            return new ApiSuccessResponse(
                $link,
                ['message' => 'Ок'],
                ResponseAlias::HTTP_OK
            );
        } catch (Throwable $e) {
            return new ApiErrorResponse('Ошибка при получение ссылки к соглашения', $e);
        }
    }
}
