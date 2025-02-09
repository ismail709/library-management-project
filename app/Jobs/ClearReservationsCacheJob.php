<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\Skip;
use Illuminate\Support\Facades\Cache;

class ClearReservationsCacheJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $user_id)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $page = 1;

        while (true) {
            if (!Cache::has('reservations_for_user_' . $this->user_id . '_page_' . $page)) {
                break;
            }
            Cache::forget('reservations_for_user_' . $this->user_id . '_page_' . $page);
            $page += 1;
        }
    }

    public function middleware()
    {
        return [
            Skip::when(!Cache::has('reservations_for_user_' . $this->user_id . '_page_1'))
        ];
    }
}
