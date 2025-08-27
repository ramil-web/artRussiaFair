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
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')
                ->comment('Идентификатор события')
                ->constrained()
                ->onDelete('cascade');
            $table->time('start_time')
                ->comment('Время начало программы');
            $table->time('end_time')
                ->comment('Время конец программы');
            $table->json('name')
                ->comment('Название программы');
            $table->json('moderator_name')
                ->comment('Имя модератора');
            $table->json('moderator_description')
                ->comment('Описание модератора')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
