<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BloodPressureReadings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blood_pressure_readings', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date');
            $table->integer('systolic');
            $table->integer('diastolic');
            $table->integer('bpm');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blood_pressure_readings');
    }
}
