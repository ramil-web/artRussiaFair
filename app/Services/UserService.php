<?php


namespace App\Services;


use Admin\Http\Filters\FiltersUserRole;
use Admin\Repositories\Manager\ManagerRepository;
use Admin\Repositories\User\UserRepository;
use App\Enums\UserRoleEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;

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

}
