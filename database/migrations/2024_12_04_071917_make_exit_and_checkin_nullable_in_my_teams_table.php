<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('my_teams', function (Blueprint $table) {
            Schema::table('my_teams', function (Blueprint $table) {
                $table->unsignedBigInteger('check_in')->nullable()->change();
                $table->unsignedBigInteger('exit')->nullable()->change();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('my_teams', function (Blueprint $table) {
            Schema::table('my_teams', function (Blueprint $table) {
                $table->unsignedBigInteger('check_in')->nullable(false)->change();
                $table->unsignedBigInteger('exit')->nullable(false)->change();
            });
        });
    }
};
