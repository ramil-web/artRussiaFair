<?php

namespace Lk\Classic\Services;

use Admin\Events\AdminMessageSent;
use App\Enums\AppStatusEnum;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Lk\Classic\Repositories\ClassicUserApplicationRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ClassicUserApplicationService
{

    public function __construct(public ClassicUserApplicationRepository $repository)
    {
    }

    /**
     * @return bool
     */
    public function checkApp(): bool
    {
        foreach (auth()->user()->userClassicApplications as $app) {
            if ($app->status !== AppStatusEnum::REJECTED()->value) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param mixed $data
     * @return Model|null
     */
    public function create(mixed $data): ?Model
    {
        // Сделать проверку, если это не первая заявка, добавить цифру к номеру
        $data['number'] = 'CUA-' . date("Y") . '-' . $data['type'] . '-' . auth()->user()->id . '-' . count(auth()->user()->userClassicApplications);
        $data['status'] = AppStatusEnum::NEW();
        $application = $this->repository->createFromArray($data);
        $dataImage = $data['image'];
        if (count($dataImage) > 0) {
            foreach ($dataImage as $image) {
                $image['classic_user_application_id'] = $application->id;
                $this->repository->create($image);
            }
        }
        return $this->repository->findById($application->id);
    }

    /**
     * @param array $data
     * @return Model|null
     */
    public function show(array $data): ?Model
    {
        $withRelation = ['classicImages'];
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
        ];
        $allowedIncludes = [
            'images',
        ];
        $withTrashed = array_key_exists('with_trashed', $data);
        return $this->repository->findById(
            $data['id'],
            $withRelation,
            $allowedFields,
            $allowedIncludes,
            $withTrashed
        );
    }


    /**
     * @param int $id
     * @return bool
     */
    public function checkStatus(int $id): bool
    {
        $userApp = $this->repository->findById($id);
        return $userApp->status !== AppStatusEnum::REJECTED()->value;
    }

    /**
     * @return string|array
     */
    public function getStatus(): string|array
    {
        $status = $this->repository->getStatus();
        if (!$status) {
            return '';
        }
        if ($status->active === false) {
            return 'У вас нет активных заявок';
        }
        return $status->toArray();
    }

    /**
     * @param array $data
     * @return Model|null
     */
    public function update(array $data): ?Model
    {
        $data['visitor'] = [];
        $userApp = $this->repository->findById($data['id']);
        $this->repository->update($userApp, $data);

        $dataImage = $data['image'];

        if (count($dataImage) !== 0) {
            foreach ($dataImage as $image) {
                $image['classic_user_application_id'] = $data['id'];
                $this->repository->updateImage($image);
            }
        }

        $this->repository->cleanImage($data['id'], Arr::pluck($dataImage, 'url'));
        $userApp = $this->repository->findById($data['id']);

        if ($userApp->status == AppStatusEnum::WAITING_AFTER_EDIT()->value) {
            broadcast(new AdminMessageSent([
                'id'     => $userApp['id'],
                'status' => $userApp['status']
            ]))->toOthers();
        }
        return $userApp;
    }

    /**
     * @param int $id
     * @return bool
     * @throws CustomException
     */
    public function checkIsNew(int $id): bool
    {
        return $this->repository->checkIsNew($id);
    }


    /**
     * @param array $data
     * @return Collection|QueryBuilder[]
     */
    public function list(array $data): Collection|array
    {
        $where = ['name' => 'user_id', 'value' => Auth::id()];

        $withRelation = ['classicImages', 'classicEvents'];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('type'),
            AllowedFilter::exact('status'),
        ];
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
            'created_at',
            'updated_at',
            'time_slot_start_id',
            'visitor'
        ];
        $allowedIncludes = [
            'classicImages',
            'classicEvents'
        ];

        $allowedSorts = ['id', 'status', 'created_at', 'updated_at'];

        $perPage = array_key_exists('per_page', $data) ? $data['per_page'] : null;

        return $this->repository->getAllByFilters(
            $where,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $allowedSorts,
            false,
            $perPage
        );
    }

}
