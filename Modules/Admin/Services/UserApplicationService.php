<?php

namespace Admin\Services;

use Admin\Events\AdminMessageSent;
use Admin\Events\AdminUserApplicationConfirmed;
use Admin\Http\Filters\CategoryFilter;
use Admin\Http\Filters\VisualizationFilter;
use Admin\Repositories\UserApplication\UserApplicationRepository;
use App\Enums\AppStatusEnum;
use App\Exceptions\CustomException;
use App\Jobs\Chat\SendMessageToMailJob;
use App\Models\UserApplication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response;

class UserApplicationService
{


    public function __construct(
        public UserApplicationRepository $userApplicationRepository,
        protected UserApplication        $userApplication
    )
    {
    }

    public function list(array $dataApp): Collection|LengthAwarePaginator
    {
        $withRelation = ["visualizationAssessment", "assessment"];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::custom('category', new CategoryFilter()),
            AllowedFilter::exact('type'),
            AllowedFilter::exact('status'),
            AllowedFilter::exact('representative_email'),
            AllowedFilter::custom('visualization', new VisualizationFilter()),
        ];

        $sort = array_key_exists('sort', $dataApp) ? $dataApp['sort'] : '-created_at';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        $allowedIncludes = [
            'images',
            'events',
            'comment',
            'comment.user',
            'visualization_count'
        ];

        $allowedFields = [
            'id',
            'type',
            'name_gallery',
            'representative_surname',
            'representative_city',
            'representative_email',
            'representative_phone',
            'status',
            'active',
            'created_at',
            'updated_at',
            'visitor',
            'event_id',
        ];

        return $this->userApplicationRepository->getUserApplications(
            $sort,
            $withRelation,
            $allowedFields,
            $allowedFilters,
            $allowedIncludes,
            $this->userApplication,
            $perPage,
            $page
        );
    }


    public function show(int $id, Request $request)
    {
        $withRelation = ['images', 'events', 'comment', 'visualization', 'user'];
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

        $request->has('with_trashed') ? $withTrashed = true : $withTrashed = false;

        $allowedIncludes = [];
        return $this->userApplicationRepository->findUserAppById(
            $id,
            $withRelation,
            $allowedFields,
            $allowedIncludes,
            $withTrashed
        );
    }

    /**
     * @param $id
     * @param $dataApp
     * @return Model|null
     * @throws CustomException
     */
    public function updateFromArray($id, $dataApp): ?Model
    {
        $data['status'] = $dataApp['status'];

        $userApp = $this->userApplicationRepository->findUserAppById($id);

        if ($userApp->status == AppStatusEnum::REJECTED()->value) {
            throw  new CustomException("Вы не можете редактировать отклоненную заявку!", Response::HTTP_FORBIDDEN);
        }

        $this->userApplicationRepository->update($userApp, $dataApp);

        /**
         * When changing the status of the application to approved, we create an order
         */
        if ($data['status'] == AppStatusEnum::CONFIRMED) {
            event(new AdminUserApplicationConfirmed($id));
        }


        $user = $this->userApplicationRepository->findUser($userApp->user_id);
        $userApp = $this->userApplicationRepository->findUserAppById($id);

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

    /**
     * @param array $dataApp
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function addVisitor(array $dataApp): Model|Collection|Builder|array|null
    {
        return $this->userApplicationRepository->visitor($dataApp);
    }
}
