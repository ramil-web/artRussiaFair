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
        Schema::table('commission_assessments', function (Blueprint $table) {
            if (!Schema::hasColumn('commission_assessments', 'user_application_id')) {
                $table->unsignedBigInteger('user_application_id');
                $table->foreign('user_application_id')->references('id')->on('user_applications');
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
            if (Schema::hasColumn('commission_assessments', 'user_application_id')) {
                $table->dropForeign(['user_application_id']);
                $table->dropColumn('user_application_id');
            }
        });
    }
};
