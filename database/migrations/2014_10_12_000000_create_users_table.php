<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // designates a user as an Admin user.
            $table->boolean('admin')->default(false);

            // if set to true the user will be forced to login again the next time they use the app.
            $table->boolean('forced_logout')->default(false);

            // is set on login and logout by SetLLOTimestampToNow and SetSetLLOTimestampToNull.
            $table->timestamp('llo_timestamp')->nullable()->default(null);

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('users');
    }
}
