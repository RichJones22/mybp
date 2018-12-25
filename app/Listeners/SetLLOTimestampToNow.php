<?php

namespace App\Listeners;

use App\Domain\State\GlobalInstance;
use App\Domain\State\UsersLoggedIn;
use App\User;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;

class SetLLOTimestampToNow
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        /** @var User $user */
        $user = auth()->user();

        session()->put('user_id', auth()->user()->id);

        $user->setAttribute('llo_timestamp', Carbon::now());

        $user->save();
    }
}
