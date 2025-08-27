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
        Schema::table('classic_user_applications', function (Blueprint $table) {
            // Удаляем старый внешний ключ и столбец
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');

            // 1. Добавляем колонку, но пока она может быть NULL
            $table->foreignId('classic_event_id')->nullable()->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('classic_user_applications', function (Blueprint $table) {
            // Откатываем изменения: удаляем новый внешний ключ
            $table->dropForeign(['classic_event_id']);
            $table->dropColumn('classic_event_id');

            $table->foreignId('event_id')->nullable()->constrained('events')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
};
