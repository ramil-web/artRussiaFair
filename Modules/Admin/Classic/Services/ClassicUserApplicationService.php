<?php

namespace Admin\Classic\Services;

use Admin\Classic\Repositories\ClassicUserApplicationRepository;
use Admin\Events\AdminMessageSent;
use Admin\Events\AdminUserApplicationConfirmed;
use App\Enums\AppStatusEnum;
use App\Exceptions\CustomException;
use App\Jobs\Chat\SendMessageToMailJob;
use App\Models\ClassicUserApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class ClassicUserApplicationService
{
    public function __construct(
        public ClassicUserApplicationRepository $repository,
        public ClassicUserApplication           $application
    )
    {
    }

    /**
     * @param mixed $data
     * @return Collection|LengthAwarePaginator
     */
    public function list(mixed $data): Collection|LengthAwarePaginator
    {
        $withRelation = [];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('type'),
            AllowedFilter::exact('status'),
            AllowedFilter::exact('representative_email'),
        ];

        $sort = array_key_exists('sort', $data) ? $data['sort'] : '-created_at';
        $perPage = array_key_exists('per_page', $data) ? $data['per_page'] : null;
        $page = array_key_exists('page', $data) ? $data['page'] : null;

        $allowedIncludes = [
            'classicImages',
            'events',
        ];

        $allowedFields = [
            'id',
            'type',
            'name_gallery',
            'representative_surname',
            'representative_email',
            'representative_phone',
            'status',
            'active',
            'created_at',
            'updated_at',
            'visitor'
        ];

        return $this->repository->getUserApplications(
            $sort,
            $withRelation,
            $allowedFields,
            $allowedFilters,
            $allowedIncludes,
            $this->application,
            $perPage,
            $page
        );
    }

    public function show(array $data): Model|Collection|QueryBuilder|array|null
    {
        $withRelation = ['classicImages', 'events', 'user'];
        $allowedFields = [
            'id',
            'type',
            'name_gallery',
            'representative_name',
            'representative_surname',
            'representative_email',
            'representative_phone',
            'representative_city',
            'about_style',
            'about_description',
            'other_fair',
            'social_links',
            'status',
            'active',
            'created_at',
            'updated_at'
        ];

        $allowedIncludes = [];
        return $this->repository->findUserAppById(
            $data['id'],
            $withRelation,
            $allowedFields,
            $allowedIncludes
        );
    }

    /**
     * @throws CustomException
     */
    public function update(array $dataApp)
    {
        $data['status'] = $dataApp['status'];

        $userApp = $this->repository->findUserAppById($dataApp['id']);

        if ($userApp->status == AppStatusEnum::REJECTED()->value) {
            throw  new CustomException("Вы не можете редактировать отклоненную заявку!", Response::HTTP_FORBIDDEN);
        }

        $dataApp['active'] = !($dataApp['status'] == AppStatusEnum::REJECTED()->value);
        $this->repository->update($userApp, $dataApp);

        /**
         * When changing the status of the application to approved, we create an order
         */
        if ($data['status'] == AppStatusEnum::CONFIRMED) {
            event(new AdminUserApplicationConfirmed($dataApp['id']));
        }


        $user = User::query()->findOrFail($userApp->user_id);
        $userApp = $this->repository->findUserAppById($dataApp['id']);

        broadcast(new AdminMessageSent([
            'id'     => $userApp['id'],
            'status' => $userApp['status']
        ]))->toOthers();

        SendMessageToMailJob::dispatch(
            'Статус заявки',
            'emails.user-app-status',
            [
                'email' => $user->email,
                'url'   => env('LK_LINK')
            ]
        )->delay(now()->addSeconds(3));

        return $userApp;
    }
}
