<?php

declare(strict_types=1);

namespace Tests\Browser\Admin;

use App\Exceptions\AdminCantChangeTheirAdminFlag;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminTest extends DuskTestCase
{
    use WithFaker, DatabaseMigrations;

    /**
     * @throws \Throwable
     */
    public function test_admin_cant_change_their_admin_value()
    {
        $adminEmail = 'bob@bob.com';

        $this->browse(function (Browser $browser) use ($adminEmail) {
            /** @var User $admin */
            $admin = create(User::class, [
                'email' => $adminEmail,
                'admin' => true,
            ]);

            $browser->loginAs($admin)
                ->visit('/home')
                ->assertSee('BloodPressureReadings')
                ->assertSee('Users');

            $browser->clickLink('Users')      // route to Users page
                ->waitForText($adminEmail)        // wait for page to display
                ->click('@1-edit-button')  // click the edit button
                ->waitForText('Admin')        // wait for edit page to display
//                ->screenshot('sample1')
                ->click('#admin > div')    // click the admin checkbox
//                ->screenshot('sample2')
                ->press('Update User')      // press the update button
                ->waitForText(AdminCantChangeTheirAdminFlag::MESSAGE)
                ->assertSee(AdminCantChangeTheirAdminFlag::MESSAGE)
//                ->screenshot('sample3')
            ;

            /** @var User $adminValue */
            $user = User::query()
                ->where('email', $adminEmail)
                ->first();

            $this->assertTrue((bool) $user->getAttribute('admin'));
        });
    }

    /**
     * @throws \Throwable
     */
    public function test_non_admin_cant_see_the_users_resource()
    {
        $userEmail = 'bob@bob.com';

        $this->browse(function (Browser $browser) use ($userEmail) {
            /** @var User $admin */
            $admin = create(User::class, [
                'email' => $userEmail,
                'admin' => false,
            ]);

            $browser->loginAs($admin)
                ->visit('/home')
                ->assertSee('BloodPressureReadings')
                ->assertDontSee('Users');
        });
    }

    /**
     * @throws \Throwable
     */
    public function test_admin_can_change_another_users_admin_value()
    {
        $adminEmail = 'admin@admin.com';
        $userEmail = 'john@doe.com';

        $this->browse(function (Browser $browser) use ($adminEmail, $userEmail) {
            /** @var User $admin */
            $admin = create(User::class, [
                'email' => $adminEmail,
                'admin' => true,
            ]);

            create(User::class, [
                'name' => 'John',
                'email' => $userEmail,
                'admin' => false,
            ]);

            $browser->loginAs($admin)
                ->visit('/home')
                ->assertSee('BloodPressureReadings')
                ->assertSee('Users');

            $browser->clickLink('Users')      // route to Users page
                ->waitForText($userEmail)         // wait for page to display
                ->click('@2-edit-button')  // click the edit button
                ->waitForText('Admin')        // wait for edit page to display
//                ->screenshot('sample1')
                ->click('#admin > div')    // click the admin checkbox
//                ->screenshot('sample2')
                ->press('Update User')      // press the update button
                ->waitForText('The user was updated!')
                ->assertSee('The user was updated!')
                ->waitForText('User Details') // wait for User Details page to display.
//                ->screenshot('sample3')
            ;

            /** @var User $user */
            $user = User::query()
                ->where('email', $userEmail)
                ->first();

            $this->assertTrue((bool) $user->getAttribute('admin'));
        });
    }

    /**
     * @throws \Throwable
     */
    public function test_once_admin_can_change_another_users_admin_value_other_user_is_forced_to_logoff()
    {
        $adminEmail = 'admin@admin.com';
        $userEmail = 'john@doe.com';

        $this->browse(function (Browser $browser, Browser $browser2) use ($adminEmail, $userEmail) {
            /** @var User $admin */
            $admin = create(User::class, [
                'email' => $adminEmail,
                'admin' => true,
            ]);

            /** @var User $user */
            $user = create(User::class, [
                'name' => 'John',
                'email' => $userEmail,
                'admin' => false,
            ]);

            $browser2->loginAs($user)
                ->visit('/home')
                ->assertSee('BloodPressureReadings')
                ->waitForText('BloodPressureReadings')
                ->assertDontSee('Users')
//                ->screenshot('sample1')
            ;

            $browser->loginAs($admin)
                ->visit('/home')
                ->assertSee('BloodPressureReadings')
                ->waitForText('BloodPressureReadings')
                ->assertSee('Users')
//                ->screenshot('sample2')
            ;

            $browser->clickLink('Users')          // route to Users page
                ->waitForText($userEmail)             // wait for page to display
                ->click('@2-edit-button')      // click the edit button
                ->waitForText('Admin')            // wait for edit page to display
                ->screenshot('sample3')
                    ->click('#admin > div')    // click the admin checkbox
    //                ->screenshot('sample4')
                    ->press('Update User')      // press the update button
                    ->waitForText('The user was updated!')
                    ->assertSee('The user was updated!')
                    ->waitForText('User Details') // wait for User Details page to display.
    //                ->screenshot('sample5')
            ;

            $browser2
//                ->screenshot('sample7')
                ->clickLink('BloodPressureReadings')
                ->waitForText('Remember Me', 20)
                ->assertSee('Remember Me')
//                ->screenshot('sample8')
            ;
        });
    }
}
