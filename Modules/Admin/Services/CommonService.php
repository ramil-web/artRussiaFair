<?php

namespace Admin\Services;

use Carbon\Carbon;

class CommonService
{
    /**
     * @param array $dataApp
     * @return array
     */
    public function dateInterval(array $dataApp): array
    {
        $current = Carbon::now();
        $start = new Carbon("first day of January $current->year");

        $from = array_key_exists('filter', $dataApp) &&
        array_key_exists('from', $dataApp['filter']) ? $dataApp['filter']['from'] : $start;

        $to = array_key_exists('filter', $dataApp) &&
        array_key_exists('to', $dataApp['filter']) ? $dataApp['filter']['to'] : $current;
        return [$from, $to];
    }
}
