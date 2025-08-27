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
        Schema::create('program_partners', function (Blueprint $table) {
            $table->bigInteger('program_id')
                ->comment('Идентификатор программ');
            $table->bigInteger('partner_id')
                ->comment('Идентификатор партнера/ов');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('program_partners');
    }
};
