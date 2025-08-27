<?php


namespace Admin\Services;


use Admin\Http\Requests\Auth\ForgotPasswordRequest;
use Admin\Http\Requests\Auth\ResetPasswordRequest;
use App\Enums\UserRoleEnum;
use App\Jobs\Mail\SendRegistrationMailJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function registration(array $userData): User
    {
//        $userData = $request->validated();
//
        $password = Str::random(10);
//        $userData['password'] = $password;
        $userData['username'] = Str::before($userData['email'], '@');
        $userData['password'] = $userData['username'];
        $user = $this->userService->create($userData);

        $user->assignRole(UserRoleEnum::PARTICIPANT);

        SendRegistrationMailJob::dispatch($user->email, $password);

        return $user;
    }

    /**
     * Временный метод
     *
     * @param ForgotPasswordRequest $request
     * @return array
     * @throws ValidationException
     */
    public function forgotPassword(ForgotPasswordRequest $request): array
    {
        $data = [];

        $status = Password::sendResetLink($request->validated(), function (User $user, string $token) use (&$data) {
            $data['token'] = $token;
            $data['email'] = $user->email;
            $user->sendPasswordResetNotification($token);
        });

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [$status]
            ]);
        }

        return $data;
    }

    public function resetPassword(ResetPasswordRequest $request): bool
    {
        $userData = $request->validated();

        $status = Password::reset(
            $userData,
            function ($user) use ($request) {
                $this->userService->resetPassword($user,$request->only('password'));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            return false;
        }

        return true;
    }

    public function getAuthUser(): Model
    {
        return $this->userService->getAuthUser();
    }
}
