<?php

namespace Lk\Services;

use App\Exceptions\CustomException;
use App\Models\InformationForPlacement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Lk\Repositories\InformationForPlacement\InformationForPlacementRepository;
use Spatie\QueryBuilder\AllowedFilter;

class InformationForPlacementService
{

    const INFORMATION_TYPES = ['for_app', 'for_catalog', 'for_social_network', 'for_general_information'];

    public function __construct(
        protected InformationForPlacementRepository $placementRepository,
        protected InformationForPlacement           $forPlacement
    )
    {
    }

    public function create(array $appData): Model
    {
        return $this->placementRepository->createData($appData);
    }

    public function list(mixed $dataApp)
    {
        $withRelation = [];
        $allowedFields = [
            'id',
            'user_application_id',
            'photo',
            'url',
            'type'
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

        return $this->placementRepository->getAllWithPaginate(
            $dataApp['id'],
            $this->forPlacement,
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
     * @param int $id
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function show(int $id): Model|Collection|Builder|array|null
    {
        return $this->placementRepository->show($id);
    }

    /**
     * @param mixed $dataApp
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function update(mixed $dataApp): Model|Collection|Builder|array|null
    {
        return $this->placementRepository->updateData($dataApp);
    }

    /**
     * @param mixed $id
     * @return bool|mixed|null
     * @throws CustomException
     */
    public function delete(mixed $id)
    {
        return $this->placementRepository->delete($id);
    }

    /**
     * @param mixed $appData
     * @return array|Builder|Collection|Model
     * @throws CustomException
     */
    public function deleteImage(mixed $appData): array|Builder|Collection|Model
    {
        return $this->placementRepository->deleteImage($appData);
    }
}
