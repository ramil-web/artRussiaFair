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
        Schema::create('program_speaker', function (Blueprint $table) {
            $table->bigInteger('program_id')
                ->comment('Идентификатор программ');
            $table->bigInteger('speaker_id')
                ->comment('Идентификатор спикера/ов');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('speaker_id')->references('id')->on('speakers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('program_speaker');
    }
};
