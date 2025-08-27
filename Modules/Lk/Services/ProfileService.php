<?php
/** @noinspection PhpUnreachableStatementInspection */

namespace Lk\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Lk\Repositories\Manager\ManagerProfileRepository;
use Lk\Repositories\User\UserProfileRepository;

class ProfileService
{

    private UserProfileRepository $userProfileRepository;
    private ManagerProfileRepository $managerProfileRepository;

    public function __construct(
        UserProfileRepository    $userProfileRepository,
        ManagerProfileRepository $managerProfileRepository
    )
    {
        $this->userProfileRepository = $userProfileRepository;
        $this->managerProfileRepository = $managerProfileRepository;
    }

    public function create(array $profileData): Model
    {
        $profileData['user_id'] = auth()->user()->id;

        return $this->userProfileRepository->create($profileData);
    }

    public function update(array $profileData)
    {
        $withRelation = ['user'];
        $allowedFields = ['id', 'avatar', 'name', 'surname', 'phone', 'city'];
        $allowedIncludes = ['user'];
        $this->userProfileRepository->update(auth()->user()->userProfile, $profileData);

        return $this->userProfileRepository->findById(
            auth()->user()->userProfile->id,
            $withRelation,
            $allowedFields,
            $allowedIncludes
        );
    }

    public function show($id)
    {
        $user = User::find($id);
        if ($user->hasAnyRole('participant', 'resident')) {
            if ($user->userProfile !== null) {
                $withRelation = ['user'];
                $allowedFields = ['id', 'avatar', 'name', 'surname', 'phone', 'city'];
                $allowedIncludes = ['user'];
                return $this->userProfileRepository->findById(
                    $user->userProfile->id,
                    $withRelation,
                    $allowedFields,
                    $allowedIncludes
                );
            }
        }
        if ($user->managerProfile !== null) {
            $withRelation = ['user'];
            $allowedFields = ['id', 'avatar', 'name', 'surname', 'phone', 'city'];
            $allowedIncludes = ['user'];
            return $this->managerProfileRepository->findById(
                $user->managerProfile->id,
                $withRelation,
                $allowedFields,
                $allowedIncludes
            );
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
