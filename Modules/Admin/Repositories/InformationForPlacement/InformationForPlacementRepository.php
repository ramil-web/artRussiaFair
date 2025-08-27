<?php

namespace Admin\Repositories\InformationForPlacement;

use Admin\Repositories\BaseRepository;
use App\Models\InformationForPlacement;
use App\Models\UserApplication;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InformationForPlacementRepository extends BaseRepository
{
    public function __construct(InformationForPlacement $model, public UserApplication $userApplication)
    {
        parent::__construct($model);
    }


    /**
     * @param $sort
     * @param $allowedFields
     * @param $perPage
     * @param $page
     * @return LengthAwarePaginator
     */
    public function informationForPlacement(
        $sort,
        $allowedFields,
        $perPage,
        $page
    ): LengthAwarePaginator
    {
        $pageName = 'page';

        $descending = $sort[0] == '-' ? 'desc' : 'asc';
        $sort = str_starts_with($sort, '-') ? substr($sort, 1) : $sort;

        $query = DB::table('information_for_placements')
            ->select(
                'information_for_placements.user_application_id',
                DB::raw("
            JSON_AGG(
                JSON_BUILD_OBJECT(
                    'id', information_for_placements.id,
                    'name', information_for_placements.name,
                    'description', information_for_placements.description,
                    'url', information_for_placements.url,
                    'type', information_for_placements.type,
                    'photo', information_for_placements.photo,
                    'social_network', information_for_placements.social_network,
                    'created_at', information_for_placements.created_at,
                    'updated_at', information_for_placements.updated_at
                )
            ) AS information_for_placements"
                ),
                DB::raw("
            JSON_AGG(
                JSON_BUILD_OBJECT(
                    'user_id', user_profiles.user_id,
                    'name', user_profiles.name,
                    'surname', user_profiles.surname
                )
            ) AS user_profile"
                )
            )
            ->leftJoin('user_applications', 'user_applications.id', '=', 'information_for_placements.user_application_id')
            ->leftJoin('user_profiles', 'user_applications.user_id', '=', 'user_profiles.user_id')
            ->groupBy('information_for_placements.user_application_id')
            ->orderBy(DB::raw("MIN($sort)"), $descending);
        return $query->paginate($perPage, $allowedFields, $pageName, $page);
    }

}
