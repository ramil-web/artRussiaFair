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
        Schema::table('information_for_placements', function (Blueprint $table) {
            $table->string('type')
                ->comment("The type for whom we upload information")
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('information_for_placements', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
