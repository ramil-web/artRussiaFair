<?php


namespace Admin\Services;

use Admin\Http\Filters\FiltersUserRole;
use Admin\Repositories\Manager\ManagerRepository;
use App\Enums\UserRoleEnum;
use App\Exceptions\CustomException;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;

/**
 *
 */
class ManagerService
{
    private ManagerRepository $managerRepository;

    public function __construct(ManagerRepository $managerRepository)
    {
        $this->managerRepository = $managerRepository;
    }

    public function create(array $userData): Model
    {
        return $this->managerRepository->create($userData);
    }

    public function update(int $id, array $Data): Model
    {
        $this->managerRepository->update(User::find($id), $Data);

        $withRelation = ['roles', 'managerProfile'];
        $allowedFields = ['id', 'username', 'email', 'roles.name', 'managerProfile.name'];
        $allowedIncludes = [
            'roles',
            AllowedInclude::relationship('profile', 'managerProfile')
        ];
        return $this->managerRepository->findById($id, $withRelation, $allowedFields, $allowedIncludes);
    }

    public function list($request)
    {
        $role = [UserRoleEnum::MANAGER, UserRoleEnum::COMMISSION, UserRoleEnum::SUPER_ADMIN];
        $withRelation = ['roles', 'managerProfile'];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::beginsWithStrict('username'),
            AllowedFilter::beginsWithStrict('email'),
            AllowedFilter::custom('roles', new FiltersUserRole()),
            AllowedFilter::trashed(),

        ];
        $allowedFields = ['id', 'username', 'email', 'roles.name', 'managerProfile.name'];
        $allowedIncludes = [
            'roles',
            AllowedInclude::relationship('profile', 'managerProfile')
        ];
        $allowedSorts = ['id', 'username', 'roles.name'];

//        $request->has('with_trashed') ? $withTrashed = true : $withTrashed = false;

//        dump($withTrashed);
        $request->has('per_page') ? $perPage = $request->per_page : $perPage = null;

        return $this->managerRepository->getUserByFilters(
            $role,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $allowedSorts,
            $perPage
        );
    }


    public function view(int $id, Request $request)
    {
        $withRelation = ['roles', 'managerProfile'];
        $allowedFields = ['id', 'username', 'email', 'roles.name', 'managerProfile.name'];
        $allowedIncludes = [
            'roles',
            AllowedInclude::relationship('profile', 'managerProfile')
        ];
        $request->has('with_trashed') ? $withTrashed = true : $withTrashed = false;

        return $this->managerRepository->findById($id, $withRelation, $allowedFields, $allowedIncludes, $withTrashed);
    }


    /**
     * @throws CustomException
     */
    public function softDelete(int $id): int
    {
        return $this->managerRepository->store($id);
    }

    public function restore(int $id): Model
    {
        $this->managerRepository->restore(User::withTrashed()->find($id));
        $withRelation = ['roles', 'managerProfile'];
        $allowedFields = ['id', 'username', 'email', 'roles.name', 'managerProfile.name'];
        $allowedIncludes = [
            'roles',
            AllowedInclude::relationship('profile', 'managerProfile')
        ];
        return $this->managerRepository->findById($id, $withRelation, $allowedFields, $allowedIncludes);
    }

    /**
     * @throws CustomException
     */
    public function forceDelete(int $id): bool
    {
        return $this->managerRepository->delete($id);
    }
}
