<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Symfony\Component\Console\Helper\Table;

class CreateBloodPressureReadings extends Migration
{
    /** @var string */
    const TABLE_NAME = 'blood_pressure_readings';

    /**
     * Run the migrations.
     *
     * @return void
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
            $table->unsignedInteger('user_id');

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::drop(self::TABLE_NAME);
    }
}
