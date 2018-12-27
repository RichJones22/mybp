<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Helper\Table;

class CreateBloodPressureReadings extends Migration
{
    /** @var string */
    const TABLE_NAME = 'blood_pressure_readings';

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();

        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date');
            $table->integer('systolic');
            $table->integer('diastolic');
            $table->integer('bpm');
            $table->timestamps();
        });

        // create foreign key to User
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            if ('mysql' === Schema::getConnection()->getDriverName()) {
                $table->unsignedInteger('user_id');
            } else { // db is sqlite, which wants the default to be 'not null'
                $table->unsignedInteger('user_id')->default('not null');
            }

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::drop(self::TABLE_NAME);
    }
}
