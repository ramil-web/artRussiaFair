<?php

use Illuminate\Database\Migrations\Migration;
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
//        Schema::create('visualization', function (Blueprint $table) {
//            $table->id();
//            $table->foreignId('user_application_id')->constrained()
//                ->onUpdate('cascade')
//                ->onDelete('cascade');
//            $table->string('url');
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visualization');
    }
};
