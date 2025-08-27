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
        Schema::table('events', function (Blueprint $table) {
            $table->dateTime('start_accepting_applications')
                ->nullable()
                ->comment("The beginning of accepting applications");
            $table->dateTime('end_accepting_applications')
                ->nullable()
                ->comment("The end of accepting applications");
        });

        Schema::table('user_applications', function (Blueprint $table) {
            $table->bigInteger('event_id')
                ->nullable()
                ->comment('The ID of event');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('start_accepting_applications');
            $table->dropColumn('end_accepting_applications');
        });

        Schema::table('user_applications', function (Blueprint $table) {
            $table->dropColumn('event_id');
        });
    }
};
