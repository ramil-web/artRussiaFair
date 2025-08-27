<?php

namespace App\Jobs\UserApp;

use App\Exceptions\CustomException;
use App\Models\ClassicUserApplication;
use App\Models\Event;
use App\Models\UserApplication;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UserAppInactiveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public int $tries = 10;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws CustomException
     */
    public function handle(): void
    {
        try {
            $this->makeUserAppInactive();
            $this->makeClassicRejectedAppInActive();
        } catch (Throwable $e) {
            if ($this->attempts() > 10) {
                throw new CustomException("Failed to send callback", 500);
            }
            $this->release(300);
            return;
        }
    }

    /**
     * @return void
     * @throws CustomException
     */
    private function makeUserAppInactive(): void
    {
        try {
            $currentDate = Carbon::parse(Carbon::now())->format('Y-m-d H:i:s');
            $inactiveEvents = Event::query()
                ->where('end_date', '<', $currentDate)
                ->pluck('id')->toArray();
            if (!empty($inactiveEvents)) {
                UserApplication::query()
                    ->whereIn('event_id', $inactiveEvents)
                    ->update(['active' => false]);
            }
            UserApplication::query()
                ->where('status', 'rejected')
                ->update(['active' => false]);
            $this->makeActive();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return void
     * @throws CustomException
     */
    private function makeActive(): void
    {
        try {
            $currentDate = Carbon::parse(Carbon::now())->format('Y-m-d H:i:s');
            $inactiveEvents = Event::query()
                ->where('end_date', '>', $currentDate)
                ->pluck('id')->toArray();
            if (!empty($inactiveEvents)) {
                UserApplication::query()
                    ->whereIn('event_id', $inactiveEvents)
                    ->where('status', '!=', 'rejected')
                    ->update(['active' => true]);
            }
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @throws CustomException
     */
    private function makeClassicRejectedAppInActive(): void
    {
        try {
            ClassicUserApplication::query()
                ->where('status', 'rejected')
                ->update(['active' => false]);
            ClassicUserApplication::query()
                ->where('status', '!=', 'rejected')
                ->update(['active' => true]);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
