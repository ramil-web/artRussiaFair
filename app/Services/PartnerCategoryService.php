<?php

namespace App\Services;

use App\Http\Filters\NameFilter;
use App\Models\PartnerCategory;
use App\Repositories\PartnerCategory\PartnerCategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;

class PartnerCategoryService
{
    public function __construct(
        protected PartnerCategoryRepository $partnerCategoryRepository,
        public PartnerCategory              $partnerCategory
    )
    {
    }

    /**
     * @param mixed $dataApp
     * @return Collection|LengthAwarePaginator
     */
    public function list(mixed $dataApp): Collection|LengthAwarePaginator
    {
        $withRelation = [];
        $allowedFields = ['id', 'name', 'created_at', 'updated_at', 'sort_id'];
        $allowedIncludes = [];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::custom('name', new NameFilter()),
            AllowedFilter::trashed(),
        ];
        $sortBy = array_key_exists('sort_by', $dataApp) ? $dataApp['sort_by'] : 'id';
        $orderBy = array_key_exists('order_by', $dataApp) ? $dataApp['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        $response =  $this->partnerCategoryRepository->getAllByFiltersAndType(
            $sortBy,
            $orderBy,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $this->partnerCategory,
            $perPage,
            null,
            $page
        );
        return $response;
    }
}
