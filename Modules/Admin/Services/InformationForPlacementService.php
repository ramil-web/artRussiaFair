<?php

namespace Admin\Services;

use Admin\Repositories\InformationForPlacement\InformationForPlacementRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InformationForPlacementService
{
    public function __construct(public InformationForPlacementRepository $repository)
    {
    }

    /**
     * @param array $dataApp
     * @return LengthAwarePaginator
     */
    public function list(array $dataApp): LengthAwarePaginator
    {
        /**
         * Order by any column ASC & DESC
         */
        $sort = array_key_exists('sort', $dataApp) ? $dataApp['sort'] : '-information_for_placements.created_at';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;


        $allowedFields = [
            'information_for_placements.id',
            'information_for_placements.user_application_id',
            'information_for_placements.name',
            'information_for_placements.description',
            'information_for_placements.url',
            'information_for_placements.type',
            'information_for_placements.photo',
            'information_for_placements.social_network',
            'information_for_placements.created_at',
            'information_for_placements.updated_at',
        ];
        $data = $this->repository->informationForPlacement($sort, $allowedFields, $perPage, $page);
        /**
         * Раскодируем json объекты, удаляем дубли
         */
        foreach ($data as $item) {
            $item->information_for_placements = json_decode($item->information_for_placements, true);
            $item->user_profile = json_decode($item->user_profile, true);


            $item->information_for_placements = collect($item->information_for_placements)->unique('id')->values()->all();
            $item->user_profile = collect($item->user_profile)->unique('id')->values()->all();
        }
        return $data;
    }
}
