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
        Schema::table('visualization_assessments', function (Blueprint $table) {
            $table->foreignId("visualization_id")
                ->nullable()
                ->constrained("visualizations")
                ->onUpdate('cascade')
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
        Schema::table('visualization_assessments', function (Blueprint $table) {
            $table->dropColumn("visualization_id");
        });
    }
};
