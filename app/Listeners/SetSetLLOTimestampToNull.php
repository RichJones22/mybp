<?php

namespace App\Listeners;

use App\User;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SetSetLLOTimestampToNull
{
    /**
     * Handle the event.
     *
     * @param  object $event
     * @return void
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
