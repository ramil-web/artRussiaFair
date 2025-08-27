<?php

namespace App\Services;

use App\Http\Filters\NewEventCategoryFilter;
use App\Http\Filters\EventTypeFilter;
use App\Http\Filters\EventYearFilter;
use App\Models\Program;
use App\Repositories\Program\ProgramRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;

class ProgramService
{
    public function __construct(
        protected ProgramRepository $programRepository,
        public Program              $program
    )
    {
    }

    public function list(mixed $dataApp): Collection|LengthAwarePaginator
    {
        $withRelation = ['speaker','partner', 'eventType'];
        $allowedFields = [
            'id',
            'event_id',
            'name',
            'moderator_name',
            'moderator_description',
            'date',
            'program_format',
            'created_at',
            'updated_at',
            'deleted_at'
        ];
        $allowedIncludes = ['speaker','partner', 'eventType'];

        $allowedFilters = [
            AllowedFilter::exact('date'),
            AllowedFilter::exact('event_id'),
            AllowedFilter::custom('event_type', new EventTypeFilter()),
            AllowedFilter::custom('year', new EventyearFilter()),
            AllowedFilter::custom('category', new NewEventCategoryFilter()),
        ];
        $sortBy = 'id';
        $orderBy = array_key_exists('order_by', $dataApp) ? $dataApp['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        return $this->programRepository->getAllByFiltersAndType(
            $sortBy,
            $orderBy,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $this->program,
            $perPage,
            null,
            $page,
        );
    }
}
