<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BloodPressureReading extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        // only display blood_pressure_readings that are for the auth()->user()
        static::addGlobalScope('user_id', function (Builder $builder) {
            $builder->where('user_id', '=', auth()->user()->id);
        });

        // assign the user_id column on the blood_pressure_table the value
        // of the auth()->user().
        static::creating(function ($query) {
            $query->user_id = auth()->user()->id;
        });
    }
}
