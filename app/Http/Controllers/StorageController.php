<?php

namespace App\Http\Controllers;

use Admin\Http\Requests\Storage\ListAdminDocRequest;
use App\Exceptions\CustomException;
use App\Services\StorageService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
     *      path="/api/v1/storage/upload",
     *      tags={"Загрузка"},
     *      security={{"bearerAuth":{}}},
     *      summary="Загрузка ",
     *      operationId="FrontUpload",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="file to upload",
     *                     property="file",
     *                     type="string",
     *                        ),
     *             @OA\Property(property="type", type="string", example="user"),
     *             )
     *         )
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
    public function upload(Request $request): JsonResponse
    {

        $path = $this->storageService->upload(
            $request->input('file'), $request->input('type')
        );

        return response()->json([
            'data' => [
                'attributes' => [
                    'path' => $path,
                    'url' => Storage::url($path),
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
     * @OA\Get(
     *    path="/api/v1/documents",
     *    tags={"App|Документы"},
     *    security={{"bearerAuth":{}}},
     *    summary="Список документов",
     *    operationId="GetDocument",
     *    @OA\Parameter(
     *           name="filter[event_id]",
     *           in="query",
     *           description="ID события",
     *           @OA\Schema(
     *              type="integer",
     *          )
     *     ),
     *      @OA\Parameter(
     *           name="sort_by",
     *           in="query",
     *           description="Сортировка по поле",
     *           @OA\Schema(
     *              type="string",
     *              enum={"id","name","created_at", "updated_at"},
     *            )
     *        ),
     *        @OA\Parameter(
     *           name="order_by",
     *           in="query",
     *           description="Порядок сортировки",
     *           @OA\Schema(
     *              type="string",
     *              enum={"ASC", "DESC"},
     *            )
     *         ),
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
    public function list(ListAdminDocRequest $request): Collection|LengthAwarePaginator|array
    {
        try {
            $dataApp = $request->validated();
            return $this->storageService->getAdminDocs($dataApp);
        } catch (Throwable $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
