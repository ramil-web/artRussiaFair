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
        Schema::table('app_comments', function (Blueprint $table) {
            $table->dropForeign('app_comments_app_id_foreign');
            $table->renameColumn('app_id', 'user_application_id');
            $table->foreign('user_application_id')->references('id')->on('user_applications');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_comments', function (Blueprint $table) {
            $table->dropForeign('app_comments_user_application_id_foreign');
            $table->renameColumn('user_application_id', 'app_id');
            $table->foreign('app_id')->references('id')->on('user_applications');
        });
    }
};
