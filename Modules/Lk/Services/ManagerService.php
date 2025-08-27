<?php


namespace Lk\Services;


use Illuminate\Http\Request;
use Lk\Repositories\Manager\ManagerRepository;
use Spatie\QueryBuilder\AllowedInclude;

//use App\Repositories\Manager\OldManagerRepository;
//use App\Repositories\Manager\ManagerRepositoryInterface;
//use App\Repositories\User\UserRepositoryInterface;

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



}
