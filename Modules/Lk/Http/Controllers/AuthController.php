<?php

namespace Lk\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use JWTAuth;
use Lk\Http\Requests\Auth\ChangePasswordRequest;
use Lk\Http\Requests\Auth\ForgotPasswordRequest;
use Lk\Http\Requests\Auth\LoginRequest;
use Lk\Http\Requests\Auth\RegistrationRequest;
use Lk\Http\Requests\Auth\ResetPasswordRequest;
use Lk\Services\AuthService;
use Lk\Services\UserService;
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
     *      path="/api/v1/lk/auth/login",
     *      tags={"Lk|Авторизация"},
     *      summary="Login",
     *      operationId="lkLogin",
     *     @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *          @OA\Property(property="login", type="string", format="email", example="participant"),
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

    public function login(LoginRequest $request)
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

        if (!User::where($login_type, $request->input('login'))->first()) {
            return new ApiErrorResponse(
                'Такого пользователя нет',
                null,
                ResponseAlias::HTTP_NOT_FOUND

            );
        }
        $token = auth()->attempt($request->only($login_type, 'password'));

        if (!$token) {
            return new ApiErrorResponse(
                'Неправильный пароль',
                null,
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY

            );
        }
        if (auth()->user()->hasRole(['manager'])) {
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

        $credentials = $request->only($login_type, 'password');


        // Попытка аутентификации
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
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

        $token = str_replace('Bearer ', '', $token);

        if(!auth()->user()) {
            return response()->json(['error' => 'TNo user with such a token was found.'], 400);
        }

        if (!auth()->user()->hasRole(['participant', 'resident'])) {
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
     * @OA\Post(
     *      path="/api/v1/lk/auth/registration",
     *      tags={"Lk|Авторизация"},
     *      summary="Registration",
     *      operationId="lkRegistration",
     *     @OA\RequestBody(
     *        required=true,
     *       @OA\MediaType(
     *        mediaType="application/vnd.api+json",
     *        @OA\Schema(
     *          required={"email"},
     *          @OA\Property(property="email", type="string", format="email", example="pupukin@mail.ru"),
     *
     *        )
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
    public function registration(RegistrationRequest $request): JsonResponse
    {
        $userData = $request->validated();

        $this->authService->registration($userData);

        return response()->json([
            'success' => true,
        ]);
    }


    /**
     * @OA\Post(
     *      path="/api/v1/lk/logout",
     *      tags={"Lk|Авторизация"},
     *      security={{"bearerAuth":{}}},
     *      summary="Logout",
     *      operationId="lkLogout",
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
    public function logout()
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
     *      path="/api/v1/lk/refresh",
     *      tags={"Lk|Авторизация"},
     *      security={{"bearerAuth":{}}},
     *      summary="Обновить токен",
     *      operationId="lkRefresh",
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
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth()->refresh(), auth()->user(), auth()->user()->userProfile);
    }

    /**
     * @param string $token
     * @param Model $user
     * @param bool $profile
     * @return ApiSuccessResponse
     */
    protected function respondWithToken(string $token, Model $user, bool $profile): ApiSuccessResponse
    {
        $result = [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60,
        ];

        return new ApiSuccessResponse(
            $result,
            ['message' => 'Auth success', 'role' => $user->getRoleNames(), 'profile' => $profile],
            ResponseAlias::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *      path="/api/v1/lk/auth/forgot-password",
     *      tags={"Lk|Авторизация"},
     *      summary="Метод для получения ссылки на восстановление пароля",
     *      operationId="lkforgot-password",
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
     *
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
     *      path="/api/v1/lk/auth/reset-password",
     *      tags={"Lk|Авторизация"},
     *      summary="Метод для сброса пароля",
     *      operationId="lkreset-password",
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
     *        name="confirm_password",
     *        in="query",
     *        required=true,
     *        @OA\Schema(
     *           type="string",
     *        )
     *    ),
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
     *      path="/api/v1/lk/change-password",
     *      tags={"Lk|Авторизация"},
     *     security={{"bearerAuth":{}}},
     *      summary="Метод для смены пароля (пользователь авторизован)",
     *      operationId="lk-change-password",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/vnd.api+json",
     *             @OA\Schema(
     *                 @OA\Property(property="old_password", type="string", description="Старый пароль" ),
     *                 @OA\Property(property="new_password", type="string",description="Новый пароль"),
     *                 @OA\Property(property="confirm_password", type="string", description="Новый пароль подтверждение" ),
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

}
