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
        Schema::create('my_teams', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('square')
                ->nullable()
                ->comment('Площадь стенда');
            $table->foreignId('user_application_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('check_in')
                ->nullable()
                ->constrained('time_slot_start')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->foreignId('exit')
                ->constrained('time_slot_start')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('my_teams');
    }
};
