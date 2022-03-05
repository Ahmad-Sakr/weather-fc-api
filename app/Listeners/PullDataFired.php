<?php

namespace App\Listeners;

use App\Events\PullData;
use App\Models\Log;

class PullDataFired
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\PullData  $event
     * @return void
     */
    public function handle(PullData $event)
    {
        Log::query()->create([
            'pull_date'     => $event->date,
            'pull_city'     => ($event->city) ? $event->city->name : 'All',
            'description'   => 'Pull Data By User'
        ]);
    }
}
