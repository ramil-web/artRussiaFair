<?php

namespace App\Services;

use Admin\Http\Filters\EventCategoryFilter;
use Admin\Http\Filters\NameFilter;
use App\Exceptions\CustomException;
use App\Http\Filters\EventFilter;
use App\Models\Participant;
use App\Repositories\Participant\ParticipantRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;

class ParticipantService
{

    public function __construct(
        protected ParticipantRepository $participantRepository,
        public Participant              $participant
    )
    {
    }

    /**
     * @param array $dataApp
     * @return Collection|LengthAwarePaginator
     */
    public function list(array $dataApp): Collection|LengthAwarePaginator
    {
        $withRelation = ['eventgable'];
        $allowedFields = [
            'id',
            'slug',
            'sort_id',
            'type',
            'name',
            'description',
            'image',
            'images',
            'stand_id',
            'created_at',
            'updated_at',
            'deleted_at'
        ];
        $allowedIncludes = [];

        $allowedFilters = [
            AllowedFilter::exact('slug'),
            AllowedFilter::custom('name', new NameFilter()),
            AllowedFilter::custom('event_id', new EventFilter()),
            AllowedFilter::custom('category', new EventCategoryFilter()),
        ];
        $sortBy = array_key_exists('sort_by', $dataApp) ? $dataApp['sort_by'] : 'sort_id';
        $orderBy = array_key_exists('order_by', $dataApp) ? $dataApp['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        $response = $this->participantRepository->getAllByFiltersAndType(
            $sortBy,
            $orderBy,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $this->participant,
            $perPage,
            null,
            $page,
            $dataApp['category'] ?? []
        );
        foreach ($response as $val) {
            $this->jsonDecode($val);
        }
        return $response;
    }

    /**
     * @param string $slug
     * @return Model|null
     * @throws CustomException
     */
    public function show(string $slug): ?Model
    {
        $allowedFields = ['name', 'description', 'stand_id'];
        $response = $this->participantRepository->findBySlug($slug, $allowedFields);
        $this->jsonDecode($response);
        return $response;
    }

    private function jsonDecode(Model|array &$created): Model|array
    {
        $created['images'] = json_decode($created['images']);
        $created['name'] = json_decode($created['name']);
        $created['description'] = json_decode($created['description']);
        return $created;
    }
}
