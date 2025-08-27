<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_slot_start', function (Blueprint $table) {
            $table->enum('action', ['check_in', 'exit'])
                ->default('check_in')
                ->comment("Слоты для заезда или выезда");
            $table->bigInteger('event_id')
                ->nullable()
                ->comment('Привязываем слот с событием');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_slot_start', function (Blueprint $table) {
            $table->dropColumn('action');
            $table->dropColumn('event_id');
        });
    }
};
