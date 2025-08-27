<?php

namespace Admin\Http\Controllers;

//use App\Http\Controllers\ApiBaseController;
use Admin\Http\Resources\UserProfile\UserProfileResource;
use Admin\Services\ProfileService;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;

class UserProfileController extends Controller
{


    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }


    /**
     * @OA\Get(
     *      path="/api/v1/admin/users/{id}/profile",
     *      operationId="ViewUserProfile",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Участники"},
     *      summary="Просмотр профиля участника",
     *@OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *           )
     * ),
     * @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     * @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     **/
    public function show(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        if ($this->profileService->checkProfile($id)) {
            return new ApiSuccessResponse(
                new  UserProfileResource($this->profileService->show($id)),
                ['message' => 'Успешно'],
                Response::HTTP_OK
            );
        }
        return new ApiErrorResponse(
            'Профиль еще не создан',
            null,
            Response::HTTP_NOT_FOUND

        );
    }

}
