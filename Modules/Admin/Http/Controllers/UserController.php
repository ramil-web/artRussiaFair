<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\User\ListRequest;
use Admin\Http\Requests\User\UpdateUserRequest;
use Admin\Http\Resources\Managers\ManagerResource;
use Admin\Http\Resources\User\UserCollection;
use Admin\Http\Resources\User\UserResource;
use Admin\Repositories\User\UserRepository;
use Admin\Services\UserService;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;


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
     *    path="/api/v1/admin/users",
     *    operationId="ResidentAll",
     *    security={{"bearerAuth":{}}},
     *    tags={"Admin|Участники"},
     *    summary="Получить список всех участников",
     *    @OA\Parameter(
     *       name="filter[trashed]",
     *       in="query",
     *       description="Показать удаленных(архивных) (with/only)",
     *       @OA\Schema(
     *          type="string",
     *          enum={"with","only"},
     *             )
     *         ),
     *       @OA\Parameter(
     *         name="filter[roles]",
     *         in="query",
     *         description="Фильтр по ролям доступные для менеджеров participant,resident",
     *         @OA\Schema(
     *               type="string",
     *               enum={"participant","resident"},
     *             )
     *     ),
     *       @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="Номер страницы",
     *          @OA\Schema(
     *                   type="integer",
     *                   example=1
     *               )
     *           ),
     *       @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          description="Количество элементов на странице",
     *          @OA\Schema(
     *                 type="integer",
     *                  example=10
     *               )
     *          ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
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
     * )
     **/
    public function index(ListRequest $request): UserCollection
    {
        $appData = $request->validated();
        return new UserCollection($this->userService->list($appData));
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/admin/users/{id}",
     *      operationId="UpdateUser",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Участники"},
     *      summary="Редактирование пользователя",
     *      @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *     @OA\Parameter(
     *      name="username",
     *      in="query",
     *       description="Юзернейм",
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="email",
     *        @OA\Schema(
     *               type="string",
     *          )
     *     ),
     *      @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Роль",
     *        @OA\Schema(
     *               type="string",
     *               enum={"participant","resident"},
     *             )
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

    public function update(int $userId, UpdateUserRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $userData = $request->validated();
            $role = $userData['role'];
            $user = $this->userService->update($userId, $userData);
            $user->assignRole($role);
            return new ApiSuccessResponse(
                new ManagerResource($user),
                ['message' => 'Успешно'],
                ResponseAlias::HTTP_OK
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
     *      path="/api/v1/admin/users/{id}/archive",
     *      operationId="ArchiveUser",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Участники"},
     *      summary="Добавить пользователя в архив",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *          type="integer"
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
     * @param int $id
     * @return ApiSuccessResponse|ApiErrorResponse
     * @throws CustomException
     */
    public function archive(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        if ($this->checkUser($id)) {
            $this->userService->softDelete($id);
            return new ApiSuccessResponse(
                null,
                ['message' => 'Успешно добавлен в архив'],
                ResponseAlias::HTTP_OK
            );
        }
        return new ApiErrorResponse(
            'Такого пользователя не существует',
            null,
            ResponseAlias::HTTP_NOT_FOUND
        );
    }

    public function checkUser(int $id): bool
    {
        if (User::withTrashed()->find($id) and User::withTrashed()->find($id)->hasRole(['participant', 'resident'])) {
            return true;
        }
        return false;
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/admin/users/{id}/restore",
     *      operationId="RestoreUser",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Участники"},
     *      summary="Восстановить пользователя из архива",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *           type="integer"
     *           )
     *     ),
     *      @OA\Response(response=401,description="Unauthenticated"),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404,description="Not Found",
     *          @OA\JsonContent(
     *             @OA\Property(property="errors", type="array",
     *             @OA\Items(
     *                @OA\Property(property="status", example="404"),
     *                @OA\Property(property="detail", example="Such an user does not exist in the archive."),
     *               ),
     *            ),
     *         ),
     *       ),
     *        @OA\Response(response=403,description="Forbidden",
     *           @OA\JsonContent(
     *              @OA\Property(property="message", example="User does not have the right roles.")
     *         ),
     *      ),
     *      @OA\Response(response=500,description="Server error, not found")
     *      ),
     *    ),
     * @param int $id
     * @param Request $request
     * @return ApiSuccessResponse|ApiErrorResponse
     * @throws CustomException
     */
    public function restore(int $id, Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        if ($this->checkUser($id)) {
            $this->userService->restore($id);
            return new ApiSuccessResponse(
                new UserResource($this->userService->view($id, $request)),
                ['message' => 'успешно восстановлен'],
                ResponseAlias::HTTP_OK
            );
        }
        return new ApiErrorResponse(
            'Такого пользователя не существует',
            null,
            ResponseAlias::HTTP_NOT_FOUND
        );
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/users/{id}",
     *      operationId="GetUser",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Участники"},
     *      summary="Получить данные пользователя",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *     @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *         @OA\Schema(
     *             type="integer"
     *           )
     * )
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
    public function show(int $id, Request $request): ApiErrorResponse|ApiSuccessResponse
    {
        if (!$this->userService->view($id, $request)) {
            return new ApiErrorResponse(
                'Такого пользователя нет или он находится в архиве',
                null,
                ResponseAlias::HTTP_NOT_FOUND

            );
        }
        return new ApiSuccessResponse(
            new ManagerResource($this->userService->view($id, $request)),
            ['message' => 'OK'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/admin/users/{id}",
     *      operationId="DeleteUser",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Участники"},
     *      summary="Полностью удалить пользователя и связанные данные",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
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
     * @param int $id
     * @return ApiSuccessResponse|ApiErrorResponse
     * @throws CustomException
     */
    public function destroy(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        if ($this->checkUser($id)) {
            $this->userService->delete($id);
            return new ApiSuccessResponse(
                null,
                ['message' => 'Пользователь успешно полностью удален'],
                ResponseAlias::HTTP_NO_CONTENT
            );
        }
        return new ApiErrorResponse(
            'Такого пользователя не существует',
            null,
            ResponseAlias::HTTP_NOT_FOUND
        );
    }
}
