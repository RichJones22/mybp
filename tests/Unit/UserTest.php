<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\AdminCantChangeTheirAdminFlag;
use App\Http\Middleware\CheckUserStateChange;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations, AuthenticatesUsers;

    public function test_admin_user_cant_change_their_admin_value()
    {
        /** @var User $admin */
        $admin = create(User::class, [
            'admin' => true,
        ]);

        $this->actingAs($admin);

        $this->expectExceptionMessage(AdminCantChangeTheirAdminFlag::MESSAGE);

        $admin->setAttribute('admin', false);
        $admin->save();
    }

    public function test_non_admin_user_can_change_their_admin_value()
    {
        $userEmail = 'bob@bob.com';

        /** @var User $admin */
        $authUser = create(User::class);

        /** @var User $user */
        $user = create(User::class, [
            'email' => $userEmail,
        ]);

        $this->actingAs($authUser);

        $user->setAttribute('admin', true);
        $user->save();

        $this->assertDatabaseHas('users', [
            'email' => $userEmail,
            'admin' => true,
        ]);
    }

    public function test_user_forced_logout_value_set()
    {
        /** @var User $user */
        $user = create(User::class, [
            'forced_logout' => true,
            'email' => 'bob@bob.com',
        ]);

        Route::get('temp', function (Request $request) {
            // route temp used to invoke CheckUserStateChange middleware.
        })->middleware(CheckUserStateChange::class);

        // note:  actingAs does not perform an Events\Login event.
        //        it performs a user 'Events\Authenticated' event.
        $this->actingAs($user)
            ->get('temp');

        $this->assertDatabaseHas('users', [
            'email' => $user->getAttribute('email'),
            'forced_logout' => false,
        ]);
    }

    /**
     * On User Login, we want to reset both the forced_logout and
     * llo_timestamp.
     */
    public function test_log_user_in()
    {
        /** @var User $user */
        $user = create(User::class, [
            'llo_timestamp' => Carbon::now(),
            'forced_logout' => true,
            'email' => 'bob@bob.com',
        ]);

        // get refreshed $user from the db.
        $user->refresh();

        // check the before state
        $this->assertNotNull($user->getAttribute('llo_timestamp'));
        $this->assertTrue((bool) $user->getAttribute('forced_logout'));

        // this will perform an Events\Login event.
        $this->guard()->login($user);

        // get refreshed $user from the db.
        $user->refresh();

        // check the after state.
        $this->assertNull($user->getAttribute('llo_timestamp'));
        $this->assertFalse((bool) $user->getAttribute('forced_logout'));
    }
}
