<?php


namespace Admin\Services;

use Admin\Http\Filters\FiltersUserRole;
use Admin\Repositories\Manager\ManagerRepository;
use Admin\Repositories\User\UserRepository;
use App\Enums\UserRoleEnum;
use App\Exceptions\CustomException;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
class UserService
{

    public function __construct(protected UserRepository $userRepository)
    {
    }

    public function create(array $userData): Model
    {
        return $this->userRepository->create($userData);
    }

    /**
     * @param int $userId
     * @param array $userData
     * @return Model
     * @throws CustomException
     */
    public function update(int $userId, array $userData): Model
    {
        try {
            if (key_exists('password', $userData)) {
                $userData['password'] = Hash::make($userData['password']);
            }
            $this->userRepository->update($this->userRepository->findById($userId), $userData);
            return $this->userRepository->findById($userId);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function list($dataApp)
    {
        $role = [UserRoleEnum::PARTICIPANT, UserRoleEnum::RESIDENT];
        $withRelation = ['roles', 'userProfile'];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('username'),
            AllowedFilter::exact('email'),
            AllowedFilter::trashed(),
            AllowedFilter::custom('roles', new FiltersUserRole()),
        ];
        $allowedFields = ['id', 'username', 'email', 'roles.name', 'userProfile.name'];
        $allowedIncludes = [
            'roles',
            AllowedInclude::relationship('profile', 'userProfile')
        ];
        $allowedSorts = ['id', 'username', 'roles.name'];

        $withTrashed = array_key_exists('with_trashed', $dataApp);
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        return $this->userRepository->getUserByFilters(
            $role,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $allowedSorts,
            $withTrashed,
            $perPage,
            $page
        );
    }


    public function edit(int $id)
    {
        return $this->userRepository->getUser($id);
    }

    public function view(int $id, Request $request)
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

    public function getAuthUser(): Model
    {
        return $this->userRepository->getAuthUser();
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
    public function softDelete(int $id): bool
    {
        try {
            $model = $this->userRepository->findById($id);
            return $this->userRepository->softDelete($model);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return bool
     * @throws CustomException
     */
    public function restore(int $id): bool
    {
        try {
            $model = $this->userRepository->findById($id);
            return $this->userRepository->restore($model);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return bool
     * @throws CustomException
     */
    public function delete(int $id): bool
    {
        try {
            return $this->userRepository->forceDeleteUser($id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return bool|null
     * @throws CustomException
     */
    public function deleteParticipant(int $id): bool|null
    {
        return $this->userRepository->deleteParticipant($id);
    }
}
