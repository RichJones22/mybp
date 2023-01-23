<?php

declare(strict_types=1);

namespace Tests\Browser\Login;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserLoginTest extends DuskTestCase
{
    use WithFaker, DatabaseMigrations;

    /**
     * A Dusk test example.
     *
     * @throws \Throwable
     */
    public function test_non_admin_user_logs_in_visits_homepage_and_sees_resources()
    {
        $this->browse(function (Browser $browser) {
            /** @var User $user */
            $user = create(User::class);

            $browser->loginAs($user)
                ->visit('/home')
                ->assertSee('Blood Pressure Readings')
                ->assertDontSee('Users');
        });
    }

    /**
     * @throws \Throwable
     */
    public function test_admin_user_logs_in_visits_homepage_and_sees_users()
    {
        $this->browse(function (Browser $browser) {
            /** @var User $user */
            $user = create(User::class, [
                'admin' => true,
            ]);

            $browser->loginAs($user)
                ->visit('/home')
                ->assertSee('Blood Pressure Readings')
                ->assertSee('Users');
        });
    }
}
