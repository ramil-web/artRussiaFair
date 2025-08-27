<?php

namespace Lk\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\StorageService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Lk\Http\Requests\Storage\NewStorageRequest;
use OpenApi\Annotations as OA;

class NewStorageController extends Controller
{
    public function __construct(public StorageService $storageService)
    {
    }

    /**
     * @OA\Post(
     *      path="/api/v1/lk/storage/uploads",
     *      tags={"Lk|Загрузка файлов"},
     *      security={{"bearerAuth":{}}},
     *      summary="Загрузка файлов",
     *      operationId="lkUploads",
     *      @OA\Parameter(
     *           name="type",
     *           in="query",
     *           required=true,
     *           description="Тип, он же название директории для загрузок.",
     *           @OA\Schema(
     *               type="string",
     *               enum={
     *                      "stands","product_images","avatar","speaker","user_data","project_team","curator","artist",
     *                      "sculptor","photographer","gallery","visualization","information_for_placement","my_documents"
     *                    },
     *                  example="avatar"
     *             )
     *        ),
     *       @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                     property="file[]",
     *                     type="array",
     *                     description="файл для загрузки",
     *                 @OA\Items(
     *                    type="string",
     *                    format="binary",
     *                 ),
     *                ),
     *              ),
     *          ),
     *      ),
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
     * @throws FileNotFoundException
     */
    public function upload(NewStorageRequest $request): JsonResponse
    {
        $dataApp = $request->validated();
        $paths = $this->storageService->uploads($dataApp['file'], $dataApp['type']);
        $pathArray = [];
        foreach ($paths as $path) {
            $pathArray[] = Storage::url($path);
        }
        return response()->json([
            'data' => [
                'attributes' => [
                    'path' => $paths,
                    'url' => $pathArray
                ],
            ],
        ]);
    }
}
