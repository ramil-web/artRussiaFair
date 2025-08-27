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
        Schema::table('participants', function (Blueprint $table) {
            $table->dropPrimary("id");
            $table->string('slug')
                ->default('')
                ->comment('Уникальное поле slug');
            $table->primary(['id', 'slug']);
            $table->json('images')
                ->comment('Массив привязанных изображений с текстом')
                ->default(json_encode([]));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('images');
            $table->primary(['id']);
        });
    }
};
