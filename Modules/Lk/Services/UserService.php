<?php


namespace Lk\Services;


use App\Exceptions\CustomException;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Lk\Repositories\User\UserRepository;
use Spatie\QueryBuilder\AllowedInclude;

//use Lk\Http\Resources\User\UserCollection;
//use App\Repositories\Manager\OldManagerRepository;
//use Lk\Repositories\Manager\ManagerRepository;

/**
 *
 */
class UserService
{
    private UserRepository $userRepository;


    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

    }

    public function create(array $userData): Model
    {
        return $this->userRepository->create($userData);
    }

    public function update(array $userData): bool
    {
        if (key_exists('password', $userData)) {
            $userData['password'] = Hash::make($userData['password']);
        }

        return $this->userRepository->update(auth()->user(), $userData);
    }


    public function show(int $id, Request $request)
    {
        $withRelation = ['roles', 'userProfile'];
        $allowedFields = ['id', 'username', 'email', 'roles.name', 'userProfile.name'];
        $allowedIncludes = [
            'roles',
            AllowedInclude::relationship('profile', 'userProfile')
        ];
        $request->has('with_trashed') ? $withTrashed = true : $withTrashed = false;

        return $this->userRepository->findById($id, $withRelation, $allowedFields, $allowedIncludes, $withTrashed);
    }

    public function resetPassword(User $user, array $data): bool
    {

        return $this->userRepository->update($user, $data);
    }

    public function changePassword(User $user, array $data): bool
    {

        return $this->userRepository->update($user, ['password' => $data['new_password']]);
    }

    /**
     * @param int $id
     * @return bool
     * @throws CustomException
     */
    public function deleteUser(int $id): bool
    {
        return $this->userRepository->deleteUser($id);
    }
}
