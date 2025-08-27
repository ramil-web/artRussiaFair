<?php

namespace Admin\Repositories\MyTeam;

use Admin\Repositories\BaseRepository;
use App\Models\MyTeam;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MyTeamRepository extends BaseRepository
{
    public function __construct(MyTeam $model)
    {
        parent::__construct($model);
    }

    public function myTeams(
        mixed $sort,
        array $allowedFields,
        mixed $perPage,
        mixed $page
    ): LengthAwarePaginator|Collection
    {
        $pageName = 'page';

        $descending = $sort[0] == '-' ? 'desc' : 'asc';
        $sort = str_starts_with($sort, '-') ? substr($sort, 1) : $sort;

        /**
         * Очень много связей с сортировкой, по этому не Eloquent!
         */

        $query = DB::table('my_teams')
            ->select(
                'my_teams.id',
                'my_teams.user_application_id',
                'my_teams.square',
                'my_teams.check_in',
                'my_teams.exit',
                DB::raw("
            JSON_AGG(
                JSON_BUILD_OBJECT(
                    'id', ts.id,
                    'date', ts.date,
                    'interval_times', ts.interval_times
                )
            ) AS check_in
        "),
                DB::raw("
            JSON_AGG(
                JSON_BUILD_OBJECT(
                    'id', time_slot_start.id,
                    'date', time_slot_start.date,
                    'interval_times', time_slot_start.interval_times
                )
            ) AS exit
        "),
                DB::raw("
            JSON_AGG(
                JSON_BUILD_OBJECT(
                    'id', stand_representatives.id,
                    'full_name', stand_representatives.full_name,
                    'passport', stand_representatives.passport
                )
                ORDER BY stand_representatives.full_name $descending
            ) AS stand_representatives
        "),
                DB::raw("
            JSON_AGG(
                JSON_BUILD_OBJECT(
                    'id', builders.id,
                    'full_name', builders.full_name,
                    'passport', builders.passport
                )
                ORDER BY builders.full_name $descending
            ) AS builders
        "),
                DB::raw("
            JSON_AGG(
                JSON_BUILD_OBJECT(
                    'user_id', user_profiles.user_id,
                    'name', user_profiles.name,
                    'surname', user_profiles.surname
                )
            ) AS user_profile
        "),
                'my_teams.created_at',
                'my_teams.updated_at'
            )
            ->leftJoin('time_slot_start', 'time_slot_start.id', '=', 'my_teams.exit')
            ->leftJoin('time_slot_start as ts', 'ts.id', '=', 'my_teams.check_in')
            ->leftJoin('user_applications', 'user_applications.id', '=', 'my_teams.user_application_id')
            ->leftJoin('user_profiles', 'user_applications.user_id', '=', 'user_profiles.user_id')
            ->leftJoin('builders', 'builders.user_application_id', '=', 'my_teams.user_application_id')
            ->leftJoin('stand_representatives', 'stand_representatives.user_application_id', '=', 'my_teams.user_application_id')
            ->groupBy(
                'my_teams.id',
                'my_teams.user_application_id',
                'my_teams.square',
                'my_teams.check_in',
                'my_teams.exit',
                'my_teams.created_at',
                'my_teams.updated_at'
            )
            ->orderBy(DB::raw("MIN($sort)"), $descending);

        return $perPage !== null && $page != null
            ? $query->paginate($perPage, $allowedFields, $pageName, $page)
            : $query->get();
    }
}
