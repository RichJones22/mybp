<?php

declare(strict_types=1);

namespace App\Listeners;

use App\User;
use Carbon\Carbon;

class SetLLOTimestampToNow
{
    /**
     * On user logout.
     *
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     */
    public function handle($event)
    {
        /** @var User $user */
        $user = auth()->user();

        if (null !== $user) {
            $user->setAttribute('llo_timestamp', Carbon::now());

            $user->save();
        }
    }
}
