<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\TimeSlotStart;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TimeSlotController extends Controller
{
    public function index(Request $request)
    {
        $r = Carbon::createFromFormat('Y-m-d H:i', '2023-03-28 14:45');
        $begin = '2023-03-28 15:00';
        $end = '2023-03-30 11:30';
        $interval = CarbonInterval::minute(15);
        $period = new CarbonPeriod(
            Carbon::createFromFormat('Y-m-d H:i', $begin),
            $interval,
            Carbon::createFromFormat('Y-m-d H:i', $end)
        );
        foreach ($period as $dt) {
            echo $dt->format("H:i\n");

            TimeSlotStart::updateOrInsert(
                ['date' => $dt->format("Y-m-d"), 'interval_times' => $dt->format("H:i")],
                ['date' => $dt->format("Y-m-d"), 'interval_times' => $dt->format("H:i")]
            );
        }
    }

    public function get()
    {
        $data = [];

        $datestart = array_unique(TimeSlotStart::whereYear('date', '2023')->pluck('date')->toArray());

        foreach ($datestart as $date) {
            $interval = TimeSlotStart::where('date', $date)->where('status', true)->pluck('interval_times')->toArray();
            $data[] = ['date' => $date, 'interval_times' => $interval];
        }
        return response()->json($data);
    }

    public function countUp($date = '2023-03-28 15:00')
    {
        $up = Carbon::createFromFormat('Y-m-d H:i', $date);

        $item = TimeSlotStart::where('date', $up->format("Y-m-d"))->where('interval_times', $up->format("H:i"))->first(
        );
        DB::beginTransaction();
        if ($item->status === false) {
            DB::rollBack();
            return new ApiErrorResponse(
                'время забронировано',
                null,
                Response::HTTP_NOT_FOUND

            );
        }

        $item->increment('count');

        if ($item->count >= 2) {
            $item->update(['status' => false]);
        }
        DB::commit();

        return new ApiSuccessResponse(
            null,
            ['message' => 'Успешно'],
            Response::HTTP_OK
        );
    }

    public function checkDate($date = '2023-03-28 15:00')
    {
        $up = Carbon::createFromFormat('Y-m-d H:i', $date);

        $date = $up->format("Y-m-d");
        $interval_times = $up->format("H:i");
// Написать проверку даты заезда в заказе
        $item = TimeSlotStart::where('date', $up->format("Y-m-d"))->where('interval_times', $up->format("H:i"))->first(
        );

        $item->decrement('count');
        $item->update(['status' => true]);
    }

}
