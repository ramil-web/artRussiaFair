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
        Schema::table('orders', function (Blueprint $table) {
            $table->bigInteger('time_slot_end_id')
                ->nullable()
                ->comment("порядковый номер слота (выезд)");
            $table->string('stand_area')
                ->nullable()
                ->comment("Площадь стенда, пока small/big");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('time_slot_end_id');
            $table->dropColumn('stand_area');
        });
    }
};
