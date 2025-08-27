<?php

namespace Lk\Services;

use App\Exceptions\CustomException;
use App\Models\Visualization;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Lk\Repositories\UserApplication\UserApplicationVisualizationRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Throwable;

class AppVisualizationService
{

    public function __construct(
        protected UserApplicationVisualizationRepository $visualizationRepository,
        public Visualization                             $visualization
    )
    {
    }

    public function list(array $dataApp): Collection|LengthAwarePaginator|array
    {
        $withRelation = [];
        $allowedFields = [
            'id',
            'user_application_id',
            'url',
        ];
        $allowedIncludes = [];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('user_application_id'),
            AllowedFilter::trashed(),
        ];
        $sortBy = array_key_exists('sort_by', $dataApp) ? $dataApp['sort_by'] : 'id';
        $orderBy = array_key_exists('order_by', $dataApp) ? $dataApp['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        return $this->visualizationRepository->getAllWithPaginate(
            $dataApp['id'],
            $this->visualization,
            $allowedFilters,
            $orderBy,
            $page,
            $perPage,
            $withRelation,
            $allowedFields,
            $allowedIncludes,
            $sortBy
        );
    }

    /**
     * @param array $appData
     * @return Model
     * @throws CustomException|Throwable
     */
    public function create(array $appData): Model
    {
        return $this->visualizationRepository->create($appData);
    }

    /**
     * @param int $id
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function show(int $id): Model|Collection|Builder|array|null
    {
        return $this->visualizationRepository->show($id);
    }

    /**
     * @param int $id
     * @return bool|mixed|null
     * @throws CustomException|Throwable
     */
    public function delete(int $id): mixed
    {
        return $this->visualizationRepository->delete($id);
    }

    /**
     * @param mixed $dataApp
     * @return array|Builder|Builder[]|Collection|Model
     * @throws CustomException|Throwable
     */
    public function update(mixed $dataApp): array|Builder|Collection|Model
    {
        return $this->visualizationRepository->updateData($dataApp);
    }

    /**
     * @throws CustomException|Throwable
     */
    public function deleteImage(array $appData): bool
    {
        return $this->visualizationRepository->deleteImage($appData);
    }

    /**
     * @param int $userApplicationId
     * @return bool
     * @throws CustomException
     */
    public function checkVisualisation(int $userApplicationId): bool
    {
        return $this->visualizationRepository->checkVisualization($userApplicationId);
    }
}
