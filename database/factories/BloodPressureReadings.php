<?php

declare(strict_types=1);

use App\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(\App\BloodPressureReading::class, function (Faker $faker) {
    return [
        'date' => Carbon::now(),
        'systolic' => rand(110, 200),
        'diastolic' => rand(70, 110),
        'bpm' => rand(50, 180),
        'user_id' => create(User::class)->id,
    ];
});
