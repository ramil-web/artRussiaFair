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
        Schema::table('commission_assessments', function (Blueprint $table) {
            if (Schema::hasColumn('commission_assessments', 'app_id')) {
                $table->dropForeign(['app_id']);
                $table->dropColumn('app_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commission_assessments', function (Blueprint $table) {
            //
        });
    }
};
