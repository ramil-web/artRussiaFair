<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\Storage\DeleteAdminDocRequest;
use Admin\Http\Requests\Storage\ListAdminDocRequest;
use Admin\Http\Requests\Storage\StorageAdminDocRequest;
use Admin\Http\Requests\Storage\StorageRequest;
use Admin\Http\Requests\Storage\UpdateAdminDocsRequest;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\StorageService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Lk\Http\Requests\Storage\DeleteFileRequest;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class StorageController extends Controller
{

    private StorageService $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/storage/upload",
     *      tags={"Admin|Загрузка"},
     *      security={{"bearerAuth":{}}},
     *      summary="Загрузка изображений",
     *      operationId="adminUpload",
     *      @OA\Parameter(
     *          name="type",
     *          in="query",
     *          required=true,
     *          description="Тип, он же название директории для загрузок",
     *          @OA\Schema(
     *              type="string",
     *              enum={
     *                    "stands","product_images","avatar","speaker","user_data","project_team",
     *                    "curator","artist","sculptor","photographer","gallery","schema-of-stand"
     *                   },
     *                 example="avatar"
     *            )
     *       ),
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/vnd.api+json",
     *             @OA\Schema(
     *                 @OA\Property(property="file", type="array", description="файл для загрузки в base64",
     *                 example={"data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAICAgICAQICAgI",
     *                "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAICAgICAQICAgI"
     *                },
     *                @OA\Items(
     *                type="string"
     *                ),
     *               ),
     *             ),
     *         ),
     *     ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *              @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *    @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     */
    public function upload(StorageRequest $request): JsonResponse
    {
        $dataApp = $request->validated();
        $paths = $this->storageService->upload($dataApp['file'], $dataApp['type']);
        $pathArray = [];
        foreach ($paths as $path) {
            $pathArray[] = Storage::url($path);
        }
        return response()->json([
            'data' => [
                'attributes' => [
                    'path' => $paths,
                    'url'  => $pathArray
                ],
            ],
        ]);
    }

    /**
     * @param string $file
     * @param string $type
     * @return string $path
     */
    private function getPathFromLoad(string $file, string $type): string
    {
        return Storage::put('uploads/' . $type, $file);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/storage/doc",
     *      tags={"Admin|Документы"},
     *      security={{"bearerAuth":{}}},
     *      summary="Загрузка документа",
     *      operationId="UploadDocument",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             mediaType="application/vnd.api+json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="file to upload",
     *                     property="file",
     *                     type="string",
     *                     ),
     *             @OA\Property(property="name", type="string", example="some_doc.pdf"),
     *             @OA\Property(property="event_id", type="integer", example=1),
     *             )
     *         )
     *     ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *           @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     *      @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=400, description="Bad Request"),
     *       @OA\Response(response=404,description="Not found"),
     *       @OA\Response(response=403,description="Forbidden",
     *          @OA\JsonContent(
     *             @OA\Property(property="errors", type="array",
     *             @OA\Items(
     *                @OA\Property(property="status", example="403"),
     *                @OA\Property(property="detail", example="User does not have the right roles.")
     *             ),
     *          ),
     *       ),
     *     ),
     *     @OA\Response(response=500,description="Server error, not found")
     *)
     * @throws CustomException
     */
    public function uploadDoc(StorageAdminDocRequest $request): JsonResponse
    {
        $dataApp = $request->validated();
        $data = $this->storageService->uploadDoc($dataApp);
        return response()->json([
            'data' => [
                'attributes' => [
                    $data
                ],
            ],
        ]);
    }

    /**
     * @OA\Get(
     *    path="/api/v1/admin/get/docs",
     *    tags={"Admin|Документы"},
     *    security={{"bearerAuth":{}}},
     *    summary="Список документов",
     *    operationId="GetAdminDocument",
     *    @OA\Parameter(
     *           name="filter[event_id]",
     *           in="query",
     *           description="ID события",
     *           @OA\Schema(
     *              type="integer",
     *          )
     *     ),
     *      @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          description="Сортировка по поле",
     *          @OA\Schema(
     *             type="string",
     *             enum={"id","name","created_at", "updated_at"},
     *           )
     *       ),
     *       @OA\Parameter(
     *          name="order_by",
     *          in="query",
     *          description="Порядок сортировки",
     *          @OA\Schema(
     *             type="string",
     *             enum={"ASC", "DESC"},
     *           )
     *        ),
     *     @OA\Parameter(
     *              name="page",
     *              in="query",
     *              description="Номер страницы",
     *              @OA\Schema(
     *                 type="integer",
     *                 example=1
     *             )
     *         ),
     *        @OA\Parameter(
     *            name="per_page",
     *            in="query",
     *            description="Количество элементов на странице",
     *            @OA\Schema(
     *               type="integer",
     *                example=10
     *             )
     *        ),
     *    @OA\Response(
     *      response=200,
     *       description="Success",
     *             @OA\MediaType(
     *             mediaType="application/vnd.api+json",
     *             @OA\Schema(
     *             type="array",
     *             @OA\Items(
     *                @OA\Property(property="id",type="integer",example=1),
     *                @OA\Property(property="event_id",type="integer",example=1),
     *                @OA\Property(property="path",type="string",example="/admin-docs/2234some_doc.pdf"),
     *                @OA\Property(property="name",type="string",example="2234some_doc.pdf"),
     *                @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *             ),
     *           ),
     *       ),
     *   ),
     *      @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=400, description="Bad Request"),
     *       @OA\Response(response=404,description="Not found"),
     *       @OA\Response(response=403,description="Forbidden",
     *          @OA\JsonContent(
     *             @OA\Property(property="errors", type="array",
     *             @OA\Items(
     *                @OA\Property(property="status", example="403"),
     *                @OA\Property(property="detail", example="User does not have the right roles.")
     *             ),
     *          ),
     *       ),
     *     ),
     *     @OA\Response(response=500,description="Server error, not found")
     * )
     * @param ListAdminDocRequest $request
     * @return Collection|LengthAwarePaginator|array
     * @throws CustomException
     */
    public function getAdminDocs(ListAdminDocRequest $request): Collection|LengthAwarePaginator|array
    {
        try {
            $dataApp = $request->validated();
            return $this->storageService->getAdminDocs($dataApp);
        } catch (Throwable $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/admin/doc/delete",
     *     tags={"Admin|Документы"},
     *     security={{"bearerAuth":{}}},
     *     summary="Удаление документа",
     *     operationId="DeleteAdminDocument",
     *     @OA\Parameter(
     *        name="id",
     *        in="query",
     *        required=true,
     *        description="ID фала",
     *        @OA\Schema(
     *             type="integer"
     *        ),
     *     ),
     *     @OA\Response(
     *       response=200,
     *        description="Success",
     *               @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *       )
     *    ),
     *      @OA\Response(response=401,description="Unauthenticated"),
     *       @OA\Response(response=400, description="Bad Request"),
     *       @OA\Response(response=404,description="Not found"),
     *       @OA\Response(response=403,description="Forbidden",
     *          @OA\JsonContent(
     *             @OA\Property(property="errors", type="array",
     *             @OA\Items(
     *                @OA\Property(property="status", example="403"),
     *                @OA\Property(property="detail", example="User does not have the right roles.")
     *             ),
     *          ),
     *       ),
     *     ),
     *     @OA\Response(response=500,description="Server error, not found")
     * )
     * @param DeleteAdminDocRequest $request
     * @return JsonResponse
     * @throws CustomException
     * @throws CustomException
     */
    public function deleteDoc(DeleteAdminDocRequest $request): JsonResponse
    {
        try {
            $dataApp = $request->validated();
            return response()->json([
                'data' => [
                    'attributes' => [
                        "message" => $this->storageService->deleteDoc($dataApp['id'])
                    ],
                ],
            ]);
        } catch (Throwable $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Patch(
     *       path="/api/v1/admin/storage/doc/reload",
     *       operationId="AdminDocUpdate",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Документы"},
     *       summary="Перезагрузка файла",
     *       @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="ID документа",
     *         @OA\Schema(
     *               type="integer",
     *               example="1",
     *           )
     *       ),
     *       @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              mediaType="application/vnd.api+json",
     *              @OA\Schema(
     *                  @OA\Property(property="file",description="file to upload",type="string",),
     *                  @OA\Property(property="name", type="string", example="some_doc.pdf"),
     *                  @OA\Property(property="event_id", type="integer", example=1),
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
     *              description="ID категории",
     *              type="object",
     *                 @OA\Property(property="attributes", type="array",
     *                     @OA\Items(
     *                        @OA\Property(property="id", description="Идентификатор", type="integer", example=2),
     *                        @OA\Property(property="event_id",description="Идентификатор события",type="integer",example=2),
     *                        @OA\Property(property="name",description="Название документа",type="string",example="docs.pdf"),
     *                        @OA\Property(property="path",description="Паз",type="string",example="/admin-docs/docs.pdf"),
     *                        @OA\Property(property="link", description="Ссыллка для скачивание",type="string",example="C:\\xampp\\htdocs\\newapiartrussiafair\\storage\\/app/admin-docs/some_doc.pdf"),
     *                        @OA\Property(property="created_at", type="sting", example="2023-11-27 15:1"),
     *                       @OA\Property(property="updated_at", type="string", example="2023-11-27 15:1"),
     *                     ),
     *                   ),
     *                  ),
     *                ),
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
     * @param UpdateAdminDocsRequest $request
     * @return JsonResponse
     * @throws CustomException
     */
    public function reload(UpdateAdminDocsRequest $request): JsonResponse
    {
        $dataApp = $request->validated();
        $data = $this->storageService->update($dataApp);
        return response()->json([
            'data' => [
                'attributes' => [
                    $data
                ],
            ],
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/admin/storage/file/delete",
     *     operationId="AdminDeleteFile",
     *     security={{"bearerAuth":{}}},
     *     tags={"Admin|Загрузка"},
     *     summary="Удаление файла",
     *     @OA\Parameter(
     *         name="path",
     *         in="query",
     *         required=true,
     *         description="Адрес файла",
     *         @OA\Schema(
     *               type="string",
     *               example="/vip-guests/Гости-2024-11-12_09-14-09.xlsx",
     *           )
     *       ),
     *    @OA\Response(
     *        response=200,
     *         description="Success",
     *             @OA\MediaType(
     *             mediaType="application/vnd.api+json",
     *        )
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
     * @param DeleteFileRequest $request
     * @return JsonResponse|ApiSuccessResponse
     */
    public function deleteFile(DeleteFileRequest $request): JsonResponse|ApiSuccessResponse
    {
        $dataApp = $request->validated();
        $data = $this->storageService->delete($dataApp['path']);
        if (!$data) {
            return response()->json(
                [
                    "errors" => [
                        "status" => Response::HTTP_NOT_FOUND,
                        "detail" => "Файл с таким называнием не существует.",
                    ]
                ], Response::HTTP_NOT_FOUND);

        } else {
            return new ApiSuccessResponse(
                $data,
                ["message" => "Файл автиматический будет удалён из системы, через 30 мин."],
                Response::HTTP_OK
            );
        }
    }
}
