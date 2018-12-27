<?php

declare(strict_types=1);

namespace App\Listeners;

use App\User;

class SetSetLLOTimestampToNull
{
    /**
     * On user login.
     *
     * Handle the event.
     *
     * @param object $event
     */
    public function handle($event)
    {
        /** @var User $user */
        $user = auth()->user();

        if (null !== $user) {
            $user->setAttribute('forced_logout', false);
            $user->setAttribute('llo_timestamp', null);

            $user->save();
        }
    }
}
