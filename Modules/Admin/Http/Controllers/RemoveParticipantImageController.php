<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\Participant\RemoveImageRequest;
use Admin\Services\ParticipantService;
use App\Exceptions\CustomException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RemoveParticipantImageController extends Controller
{
    public function __construct(protected ParticipantService $participantService)
    {
    }

    /**
     * @OA\Patch(
     *       path="/api/v1/admin/participant-images/delete/{id}",
     *       operationId="AdminParticipantImagesDelete",
     *       security={{"bearerAuth":{}}},
     *       tags={"Admin|Удаление фото работ участника"},
     *       summary="Удаление фото работ партнёра",
     *       @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID партнёра",
     *         @OA\Schema(
     *               type="integer",
     *               example="1",
     *           )
     *       ),
     *       @OA\Parameter(
     *          name="image",
     *          in="query",
     *          required=true,
     *          description="Название картинки",
     *          @OA\Schema(
     *                type="string",
     *                example="http://newapiartrussiafair/api/v1/artist/nature.jpg",
     *            )
     *        ),
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
     * @param RemoveImageRequest $request
     * @return ResourceNotFoundException|ApiSuccessResponse
     * @throws CustomException
     */
    public function delete(int $id, RemoveImageRequest $request): ApiSuccessResponse|ResourceNotFoundException
    {
        try {
            $dataApp = $request->validated();
            return new ApiSuccessResponse(
                $this->participantService->deleteImage($id, $dataApp['image']),
                ['message' => 'Ok'],
                ResponseAlias::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return new ResourceNotFoundException($e);
        }
    }
}
