<?php

namespace App\Services;

use Admin\Http\Filters\EventCategoryFilter;
use App\Http\Filters\EventFilter;
use App\Http\Filters\NameFilter;
use App\Models\Speaker;
use App\Repositories\Speaker\SpeakerRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;

class SpeakerService
{
    public function __construct(
        public SpeakerRepository $speakerRepository,
        public Speaker           $speaker
    )
    {
    }

    public function list(mixed $dataApp): Collection|LengthAwarePaginator
    {
        $withRelation = ['eventgable'];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::custom('name', new NameFilter()),
            AllowedFilter::custom('event_id', new EventFilter()),
            AllowedFilter::custom('category', new EventCategoryFilter()),
        ];
        $allowedFields = ['id', 'sort_id', 'name', 'description', 'image', 'created_at', 'updated_at', 'deleted_at'];
        $allowedIncludes = [];
        $sortBy = array_key_exists('sort_by', $dataApp) ? $dataApp['sort_by'] : 'sort_id';
        $orderBy = array_key_exists('order_by', $dataApp) ? $dataApp['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        return $this->speakerRepository->getAllByFiltersAndType(
            $sortBy,
            $orderBy,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $this->speaker,
            $perPage,
            $dataApp['type'],
            $page
        );
    }

    /**
     * @param int $id
     * @return Model|null
     */
    public function show(int $id): ?Model
    {
        return $this->speakerRepository->show($id);
    }
}
