<?php

namespace Admin\Services;

use Admin\Repositories\VipGuest\VipGuestRepository;
use App\Exceptions\CustomException;
use App\Http\Filters\UserFilter;
use App\Models\VipGuest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class VipGuestService
{

    public function __construct(
        public VipGuestRepository $vipGuestRepository,
        public VipGuest           $vipGuest
    )
    {
    }

    /**
     * @param array $dataApp
     * @return Collection|QueryBuilder[]
     */
    public function list(array $dataApp)
    {

        $withRelation = ['userProfile','userApplication'];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('user_application_id'),
            AllowedFilter::custom('user_id', new UserFilter()),
        ];
        $allowedFields = [
            'id',
            'user_application_id',
            'full_name',
            'organisation',
            'email',
            'created_at',
            'updated_at'
        ];
        $allowedIncludes = [];

        /**
         * Order by any column ASC & DESC
         */
        $sort = array_key_exists('sort', $dataApp) ? $dataApp['sort'] : '-created_at';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        return $this->vipGuestRepository->getAllByFilterAndSorts(
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $perPage,
            $page,
            $sort
        );
    }

    /**
     * @param int $id
     * @return Model|null
     * @throws CustomException
     */
    public function show(int $id): Model|null
    {
        try {
            return $this->vipGuestRepository->findGuestById($id);
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }
}
