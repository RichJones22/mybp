<?php

declare(strict_types=1);

namespace App;

use App\Domain\State\CheckUserForcedLogoutState;
use App\Exceptions\AdminCantChangeTheirAdminFlag;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bloodPressureReadings()
    {
        return $this->hasMany(BloodPressureReading::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function (self $query) {
            if (null === auth()->user()) {
                return;
            }

            // you can't change your admin value.  Another admin will need
            // to do this for you.
            if ($query->getAttribute('id') === auth()->user()->id) {
                if ($query->getOriginal('admin') !== $query->getAttribute('admin')) {
                    throw new AdminCantChangeTheirAdminFlag();
                }
            }

            if ($query->getOriginal('admin') !== $query->getAttribute('admin')) {
                if ($query->getAttribute('id') === auth()->user()->id) {
                    throw new AdminCantChangeTheirAdminFlag();
                }

                // this is for none auth'ed users...
                //
                // user admin state has changed; force logoff, so that their
                // Nova panel displays correctly.

                // get user from email.
                $user = self::query()
                        ->where('email', $query->getAttribute('email'))
                        ->get()
                        ->first();

                // sets user.forced_logout to true.
                (new CheckUserForcedLogoutState())
                        ->setUser($user)
                        ->forceUserLogout();
            }
        });
    }
}
