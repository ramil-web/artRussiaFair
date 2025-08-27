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
            $table->dropForeign('commission_assessments_user_application_id_foreign');
            $table->foreign('user_application_id')
                ->references('id')
                ->on('user_applications')
                ->onDelete('cascade');
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
