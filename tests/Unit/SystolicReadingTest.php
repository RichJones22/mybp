<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\BloodPressureReading;
use App\Nova\Metrics\Trends\Readings\ReadingsBase;
use App\Nova\Metrics\Trends\Readings\ReadingsHelpers\BpColumnMaxReadingOf;
use App\Nova\Metrics\Trends\Readings\ReadingsHelpers\BpTrendResult;
use App\Nova\Metrics\Trends\Readings\Types\BpmReading;
use App\Nova\Metrics\Trends\Readings\Types\DiastolicReading;
use App\Nova\Metrics\Trends\Readings\Types\SystolicReading;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SystolicReadingTest extends TestCase
{
    use DatabaseMigrations, AuthenticatesUsers;

    const MAX_SYSTOLIC_READING = 150.0;
    const MAX_DIASTOLIC_READING = 95.0;
    const MAX_BPM_READING = 78.0;

    public $user = null;

    private $readingArray = [
        SystolicReading::class => [
            'MAX_SYSTOLIC_READING' => 150.0,
        ],
        DiastolicReading::class => [
            'MAX_DIASTOLIC_READING' => 95.0,
        ],
        BpmReading::class => [
            'MAX_BPM_READING' => 78.0,
        ],
    ];

    public function setUp()
    {
        parent::setUp();

        /* @var User $user */
        $this->user = create(User::class);

        $this->guard()->login($this->user);

        create(BloodPressureReading::class, [
            'date' => Carbon::now()->subDay(3),
            'systolic' => 140,
            'diastolic' => 90,
            'bpm' => 75,
            'user_id' => $this->user->getAttribute('id'),
        ]);

        create(BloodPressureReading::class, [
            'date' => Carbon::now()->subDay(2),
            'systolic' => self::MAX_SYSTOLIC_READING,
            'diastolic' => self::MAX_DIASTOLIC_READING,
            'bpm' => self::MAX_BPM_READING,
            'user_id' => $this->user->getAttribute('id'),
        ]);

        create(BloodPressureReading::class, [
            'date' => Carbon::now()->subDay(1),
            'systolic' => 120,
            'diastolic' => 80,
            'bpm' => 60,
            'user_id' => $this->user->getAttribute('id'),
        ]);
    }

    public function testNoReadingsData()
    {
        /* @var User $user */
        $this->user = create(User::class);

        $this->guard()->login($this->user);

        $myResult = 7.0;

        Route::get('temp', function (Request $request) use (&$myResult) {
            $myDiastolicReading = new SystolicReading();

            /** @var BpTrendResult $myResult */
            $myResult = $myDiastolicReading->calculate($request)->value;
        });

        // note:  actingAs does not perform an Events\Login event.
        //        it performs a user 'Events\Authenticated' event.
        $this->actingAs($this->user)
            ->get('temp');

        $this->assertNull($myResult);
    }

    public function testAllReadings()
    {
        foreach ($this->readingArray as $reading => $values) {
            foreach ($values as $maxReadings => $maxValue) {
                $myResult = 0;

                $myRanges = [];

                Route::get('temp', function (Request $request) use ($reading, &$myResult, &$myRanges) {
                    /** @var ReadingsBase $myDiastolicReading */
                    $myDiastolicReading = new $reading();

                    /** @var array $myRanges */
                    $myRanges = $myDiastolicReading->ranges();

                    /** @var BpTrendResult $myResult */
                    $myResult = $myDiastolicReading->calculate($request)->value;
                });

                // note:  actingAs does not perform an Events\Login event.
                //        it performs a user 'Events\Authenticated' event.
                $this->actingAs($this->user)
                    ->get('temp');

                $this->assertSame(BpColumnMaxReadingOf::BpColumnMaxReadingRanges, $myRanges);

                $this->assertSame($maxValue, $myResult);
            }
        }
    }
}
