<?php

namespace Admin\Http\Controllers;

use Admin\Http\Requests\Auth\ChangePasswordRequest;
use Admin\Http\Requests\Auth\ForgotPasswordRequest;
use Admin\Http\Requests\Auth\LoginRequest;
use Admin\Http\Requests\Auth\ResetPasswordRequest;
use Admin\Http\Requests\Participant\DeleteParticipantRequest;
use Admin\Services\AuthService;
use Admin\Services\UserService;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    private AuthService $authService;
    private UserService $userService;

    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->middleware('auth:api', [
            'except' => [
                'login',
                'registration',
                'forgotPassword',
                'resetPassword'
            ]
        ]);
        $this->authService = $authService;
        $this->userService = $userService;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/auth/login",
     *      tags={"Admin|Auth"},
     *      summary="Login",
     *      operationId="Admin.Login",
     *
     *     @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *          @OA\Property(property="login", type="string", format="email", example="admin"),
     *          @OA\Property(property="password", type="string", format="password", example="password"),
     *        )
     *      )
     *     ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *       @OA\MediaType(
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

    public function login(LoginRequest $request): ApiSuccessResponse|ApiErrorResponse|JsonResponse
    {
        $request->validated();

        $login_type = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $request->merge([
            $login_type => $request->input('login')
        ]);

        if (!$request->validated()) {
            return $this->logInByToken($request);
        }

        $token = auth()->attempt($request->only($login_type, 'password'));

        if (!$token) {
            return new ApiErrorResponse(
                'Неправильный пароль',
                null,
                ResponseAlias::HTTP_NOT_FOUND

            );
        }

        if (auth()->user()->hasRole(['participant', 'resident'])) {
            auth()->logout();
            return new ApiErrorResponse(
                'Нет прав доступа',
                null,
                ResponseAlias::HTTP_NOT_FOUND

            );
        }

        if (auth()->user()->userProfile !== null) {
            return $this->respondWithToken($token, auth()->user(), true);
        }
        return $this->respondWithToken($token, auth()->user(), false);
    }

    /**
     * @param Request $request
     * @return ApiSuccessResponse|ApiErrorResponse|JsonResponse
     */
    public function logInByToken(Request $request): ApiSuccessResponse|ApiErrorResponse|JsonResponse
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['error' => 'Authorization token is required'], 400);
        }

        if(!auth()->user()) {
            return response()->json(['error' => 'No user with such a token was found.'], 400);
        }

        $token = str_replace('Bearer ', '', $token);
        if (auth()->user()->hasRole(['participant', 'resident'])) {
            auth()->logout();
            return new ApiErrorResponse(
                'Нет прав доступа',
                null,
                ResponseAlias::HTTP_NOT_FOUND

            );
        }

        return $this->respondWithToken($token, auth()->user(), false);
    }

    /**
     * @param string $token
     * @param Model $user
     * @param bool $profile
     * @return ApiSuccessResponse
     */
    protected function respondWithToken(string $token, Model $user, bool $profile = false): ApiSuccessResponse
    {
        $result = [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60,
            'role'         => $user->getRoleNames()
        ];

        return new ApiSuccessResponse(
            $result,
            ['message' => 'Auth success', 'profile' => $profile],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/logout",
     *      tags={"Admin|Auth"},
     *      security={{"bearerAuth":{}}},
     *      summary="Logout",
     *      operationId="Admin.Logout",
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
    public function logout(): ApiSuccessResponse
    {
        auth()->logout();

        return new ApiSuccessResponse(
            [],
            ['message' => 'Logout success'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/refresh",
     *      tags={"Admin|Auth"},
     *      security={{"bearerAuth":{}}},
     *      summary="Обновить токен",
     *      operationId="adminRefresh",
     *@OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *
     *      )
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
    public function refresh(): ApiSuccessResponse
    {
        return $this->respondWithToken(auth()->refresh(), auth()->user());
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/auth/forgot-password",
     *      tags={"Admin|Auth"},
     *      summary="Метод для получения ссылки на восстановление пароля",
     *      operationId="Admin.forgot-password",
     *   @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *          required={"email"},
     *          @OA\Property(property="email", type="string", format="email", example="pupkin@mail.ru"),
     *
     *        )
     *      )
     *     ),
     *   @OA\Response(
     *       response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *
     *      )
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *    @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     * @throws ValidationException
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $data = $this->authService->forgotPassword($request);

        return response()->json([
            'data'    => $data,
            'success' => true
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/auth/reset-password",
     *      tags={"Admin|Auth"},
     *      summary="Метод для сброса пароля",
     *      operationId="Admin.reset-password",
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *
     *     ),
     *     @OA\Parameter(
     *       name="email",
     *       in="query",
     *       required=true,
     *       @OA\Schema(
     *          type="string",
     *       )
     *   ),
     *     @OA\Parameter(
     *       name="token",
     *       in="query",
     *       required=true,
     *       @OA\Schema(
     *          type="string",
     *       )
     *   ),
     *   @OA\Parameter(
     *       name="password",
     *       in="query",
     *       required=true,
     *       @OA\Schema(
     *          type="string",
     *       )
     *   ),
     *     @OA\Parameter(
     *         name="confirm_password",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *            type="string",
     *         )
     *     ),
     *   @OA\Response(
     *       response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *    @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $success = $this->authService->resetPassword($request);

        return response()->json([
            'success' => $success
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/change-password",
     *      tags={"Admin|Auth"},
     *     security={{"bearerAuth":{}}},
     *      summary="Метод для смены пароля (пользователь авторизован)",
     *      operationId="Admin-change-password",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/vnd.api+json",
     *             @OA\Schema(
     *                 @OA\Property( property="old_password", type="string", description="Старый пароль" ),
     *                 @OA\Property(property="new_password", type="string",description="Новый пароль"),
     *                 @OA\Property( property="confirm_password", type="string", description="Новый пароль подтверждение" ),
     *             )
     *         )
     *     ),
     *   @OA\Response(
     *       response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/vnd.api+json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *    @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     */
    public function changePassword(ChangePasswordRequest $request): ApiSuccessResponse
    {
        $this->userService->changePassword(Auth::user(), $request->only('new_password'));
        return new ApiSuccessResponse(
            [],
            ['message' => 'Пароль успешно обновлен'],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/admin/participant/delete",
     *      operationId="DeleteParticipant",
     *      security={{"bearerAuth":{}}},
     *      tags={"Admin|Пользователи|Участники"},
     *      summary="Полностью удалить участника и данные",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *            type="integer"
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
     *
     * @throws CustomException
     */
    public function destroy(DeleteParticipantRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        $appData = $request->validated();
        if ($this->checkUser($appData['id'])) {
            $this->userService->deleteParticipant($appData['id']);
            return new ApiSuccessResponse(
                null,
                ['message' => 'Участник успешно полностью удален'],
                ResponseAlias::HTTP_NO_CONTENT
            );
        }
        return new ApiErrorResponse(
            'Такого  участника не существует',
            null,
            ResponseAlias::HTTP_NOT_FOUND
        );
    }

    public function checkUser(int $id): bool
    {
        if (User::withTrashed()->find($id)) {
            return true;
        }
        return false;
    }
}
