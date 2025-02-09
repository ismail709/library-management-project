<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\Skip;
use Illuminate\Support\Facades\Cache;

class ClearReviewsCacheJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $book_id)
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
            if (!Cache::has('reviews_for_book_' . $this->book_id . '_page_' . $page)) {
                break;
            }
            Cache::forget('reviews_for_book_' . $this->book_id . '_page_' . $page);
            $page += 1;
        }
    }

    public function middleware()
    {
        return [
            Skip::when(!Cache::has('reviews_for_book_' . $this->book_id . '_page_1'))
        ];
    }
}
