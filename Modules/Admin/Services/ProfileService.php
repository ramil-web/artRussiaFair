<?php
/** @noinspection PhpUnreachableStatementInspection */

namespace Admin\Services;

use Admin\Repositories\Manager\ManagerProfileRepository;
use Admin\Repositories\User\UserProfileRepository;
use App\Models\User;

class ProfileService
{

    private UserProfileRepository $userProfileRepository;
    private ManagerProfileRepository $managerProfileRepository;

    public function __construct(
        UserProfileRepository $userProfileRepository,
        ManagerProfileRepository $managerProfileRepository
    ) {
        $this->userProfileRepository = $userProfileRepository;
        $this->managerProfileRepository = $managerProfileRepository;
    }

    public function create(array $profileData)
    {
        $profileData['user_id'] = auth()->user()->id;

        $this->managerProfileRepository->create($profileData);
        $withRelation = ['user'];
        $allowedFields = ['id', 'avatar', 'name', 'surname', 'phone', 'city'];
        $allowedIncludes = ['user'];
        return $this->managerProfileRepository->findById(
            auth()->user()->managerProfile->id,
            $withRelation,
            $allowedFields,
            $allowedIncludes
        );
    }

    public function update(array $profileData)
    {
        $withRelation = ['user'];
        $allowedFields = ['id', 'avatar', 'name', 'surname', 'phone', 'city'];
        $allowedIncludes = ['user'];
        if (auth()->user()->hasAnyRole('participant', 'resident')) {
            $this->userProfileRepository->update(auth()->user()->userProfile, $profileData);

            return $this->userProfileRepository->findById(
                auth()->user()->managerProfile->id,
                $withRelation,
                $allowedFields,
                $allowedIncludes
            );
        }
//        dump($profileData);
        $this->managerProfileRepository->update(auth()->user()->managerProfile, $profileData);

        return $this->managerProfileRepository->findById(
            auth()->user()->managerProfile->id,
            $withRelation,
            $allowedFields,
            $allowedIncludes
        );
    }


    public function showSelf()
    {
        $withRelation = ['user'];
        $allowedFields = ['id', 'avatar', 'name', 'surname', 'phone', 'city'];
        $allowedIncludes = ['user'];
        return $this->managerProfileRepository->findById(
            auth()->user()->managerProfile->id,
            $withRelation,
            $allowedFields,
            $allowedIncludes
        );
    }


    public function show($id)
    {
        $user = User::find($id);
//        dd($user->userProfile);
        if ($user->hasAnyRole('participant', 'resident')) {
            if ($user->userProfile !== null) {
                $withRelation = ['user'];
                $allowedFields = ['id', 'avatar', 'name', 'surname', 'phone', 'city'];
                $allowedIncludes = ['user'];
                return $this->userProfileRepository->findById($user->userProfile->id, $withRelation, $allowedFields, $allowedIncludes);
            }
        }
        if ($user->managerProfile !== null) {
            $withRelation = ['user'];
            $allowedFields = ['id', 'avatar', 'name', 'surname', 'phone', 'city'];
            $allowedIncludes = ['user'];
            return $this->managerProfileRepository->findById($user->managerProfile->id, $withRelation, $allowedFields, $allowedIncludes);
        }
        return null;
    }

    public function checkProfile(int $id): bool
    {
        $user = User::find($id);
        if ($user->userProfile !== null || $user->managerProfile !== null) {
            return true;
        }
        return false;
    }

}
