<?php

namespace Admin\Services;

use Admin\Repositories\MyTeam\MyTeamRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MyTeamService
{
    public function __construct(protected MyTeamRepository $repository)
    {
    }

    /**
     * @param array $dataApp
     * @return LengthAwarePaginator|Collection
     */
    public function list(array $dataApp):  LengthAwarePaginator|Collection
    {
        $sort = array_key_exists('sort', $dataApp) ? $dataApp['sort'] : '-my_teams.created_at';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        $allowedFields = [
            'my_teams.id',
            'my_teams.square',
            'my_teams.user_applications',
            'my_teams.check_in',
            'my_teams.exit',
            'my_teams.created_at',
            'my_teams.updated_at'
        ];

        $teams = $this->repository->myTeams($sort, $allowedFields, $perPage, $page);

        /**
         * Раскодируем json объекты, удаляем дубли
         */
        foreach ($teams as $team) {
            $team->stand_representatives = json_decode($team->stand_representatives, true);
            $team->builders = json_decode($team->builders, true);
            $team->user_profile = json_decode($team->user_profile, true);
            $team->check_in = json_decode($team->check_in, true);
            $team->exit = json_decode($team->exit, true);

            $team->builders = collect($team->builders)->unique('id')->values()->all();
            $team->stand_representatives = collect($team->stand_representatives)->unique('id')->values()->all();
            $team->user_profile = collect($team->user_profile)->unique('id')->values()->all();
            $team->check_in = collect($team->check_in)->unique('id')->values()->all();
            $team->exit = collect($team->exit)->unique('id')->values()->all();
        }
        return $teams;
    }

}
