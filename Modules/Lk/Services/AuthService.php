<?php


namespace Lk\Services;

use App\Jobs\Chat\SendMessageToMailJob;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Lk\Http\Requests\Auth\ForgotPasswordRequest;
use Random\RandomException;

class AuthService
{
    private UserService $userService;
    private $userRepository;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param array $userData
     * @return Model
     * @throws RandomException
     */
    public function registration(array $userData): Model
    {
        $userData['username'] = $userData['email'];
        $userData['password'] = bin2hex(random_bytes(10 / 2));
        $user = $this->userService->create($userData);
        $roles = Role::findByName('participant');
        $user->assignRole($roles);
        SendMessageToMailJob::dispatch(
            'Новое сообщение от Art Russia',
            'emails.registration',
            [
                'email'    => $userData['email'],
                'login'    => $userData['username'],
                'password' => $userData['password'],
                'url'      => env("LK_LINK")
            ]
        )->delay(now()->addSeconds(3));
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

    public function resetPassword(Request $request): bool
    {
        $userData = $request->all();

        $status = Password::reset(
            $userData,
            function ($user) use ($request) {
                $this->userService->resetPassword($user, $request->only('password'));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            return false;
        }
        return true;
    }
}
