<?php

namespace Lk\Http\Controllers;


use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lk\Http\Requests\Auth\DeleteUserRequest;
use Lk\Http\Requests\User\UpdateUserRequest;
use Lk\Http\Resources\User\UserResource;
use Lk\Repositories\User\UserRepository;
use Lk\Services\UserService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

//use Lk\Http\Resources\User\UserCollection;

//use Illuminate\Support\Facades\Request;

class UserController extends Controller
{

    public UserService $userService;
    public UserRepository $userRepository;

    public function __construct(UserService $userService, UserRepository $userRepository)
    {
        $this->userService = $userService;
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/lk/me",
     *      tags={"Lk|Авторизация"},
     *      security={{"bearerAuth":{}}},
     *      summary="Me",
     *      operationId="lkMe",
     * @OA\Response(
     *       response=200,
     *       description="Success",
     *              @OA\MediaType(
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
     *)
     */
    public function me(Request $request): ApiSuccessResponse
    {
        return new ApiSuccessResponse(
            new UserResource($this->userService->show(auth()->id(), $request)),
            ['message' => 'OK'],
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/lk/me",
     *      operationId="LkUpdateUser",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Авторизация"},
     *      summary="Редактирование пользователя",
     *      @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *          @OA\Property(property="username", description="Юзернейм",type="string", example="LordPupkin"),
     *          @OA\Property(property="email", type="string", format="email", example="pupukin@mail.ru"),
     *        )
     *      )
     *     ),
     *
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

    public function update(UpdateUserRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $userData = $request->validated();
            $user = $this->userService->update($userData);
            return new ApiSuccessResponse(
                new UserResource($user),
                ['message' => 'Успешно'],
                Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            return new ApiErrorResponse(
                'An error occurred while trying to create the user',
                $exception
            );
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/lk/user/delete",
     *      operationId="LkDeleteUser",
     *      security={{"bearerAuth":{}}},
     *      tags={"Lk|Авторизация"},
     *      summary="Полностью удалить пользователя и связанные данные",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *           type="integer"
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
     *      response=404,
     *      description="not found"
     *   ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     * /**
     * @param DeleteUserRequest $request
     * @return ApiSuccessResponse|ApiErrorResponse
     * @throws CustomException
     */
    public function delete(DeleteUserRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $appData = $request->validated();
            return new ApiSuccessResponse(
                $this->userService->deleteUser($appData['id']),
                ['message' => 'Пользователь успешно полностью удален'],
                ResponseAlias::HTTP_NO_CONTENT
            );
        } catch (Throwable $e) {
            throw  new CustomException($e->getMessage(), $e->getCode());
        }
    }
}
