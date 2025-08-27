<?php

namespace App\Policies;

use App\Models\ManagerProfile;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ManagerProfilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user)
    {
       if( $user->role(['super_admin'])){
           return true;
       }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User           $user
     * @param ManagerProfile $managerProfile
     * @return Response|bool
     */
    public function view(User $user, ManagerProfile $managerProfile)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User           $user
     * @param ManagerProfile $managerProfile
     * @return Response|bool
     */
    public function update(User $user, ManagerProfile $managerProfile)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User           $user
     * @param ManagerProfile $managerProfile
     * @return Response|bool
     */
    public function delete(User $user, ManagerProfile $managerProfile)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User           $user
     * @param ManagerProfile $managerProfile
     * @return Response|bool
     */
    public function restore(User $user, ManagerProfile $managerProfile)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User           $user
     * @param ManagerProfile $managerProfile
     * @return Response|bool
     */
    public function forceDelete(User $user, ManagerProfile $managerProfile)
    {
        //
    }
}
