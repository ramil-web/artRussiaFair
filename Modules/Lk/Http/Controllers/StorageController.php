<?php

namespace Lk\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Services\StorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Lk\Http\Requests\Storage\StorageRequest;
use OpenApi\Annotations as OA;

class StorageController extends Controller
{

    private StorageService $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/lk/storage/upload",
     *      tags={"Lk|Загрузка"},
     *      security={{"bearerAuth":{}}},
     *      summary="Загрузка изображений",
     *      operationId="lkUpload",
     *      @OA\Parameter(
     *           name="type",
     *           in="query",
     *           required=true,
     *           description="Тип, он же название директории для загрузок",
     *           @OA\Schema(
     *               type="string",
     *               enum={
     *                     "stands","product_images","avatar","speaker","user_data","project_team","curator","artist",
     *                     "sculptor","photographer","gallery","visualization","information_for_placement","my_documents",
     *                     "classic_stands","classic_product_images","classic_avatar","classic_speaker","classic_user_data",
     *                     "classic_project_team","classic_curator","classic_artist","classic_sculptor","classic_photographer",
     *                     "classic_gallery","classic_visualization","classic_information_for_placement","classic_my_documents"
     *                    },
     *                  example="avatar"
     *             )
     *        ),
     *       @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/vnd.api+json",
     *              @OA\Schema(
     *                  @OA\Property(property="file", type="array",
     *                     description="файл для загрузки в base64",
     *                     example={
     *                          "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAICAgICAQICAgI",
     *                           "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAICAgICAQICAgI"
     *                       },
     *                 @OA\Items(
     *                 type="string"
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
     * @throws CustomException
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
                    'url' => $pathArray
                ],
            ],
        ]);
    }


    private function getPathFromLoad($file, string $type): string
    {
        if (!Storage::put('uploads/' . $type, $file->getClientOriginalName())) {
            throw new \RuntimeException('Ошибка при сохранении файла.');
        }

        return Storage::url('uploads/' . $type.'/'. $file->getClientOriginalName());
    }
}
