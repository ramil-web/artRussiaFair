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
    public function up(): void
    {
        Schema::table('user_applications', function (Blueprint $table) {
            $table->json('visitor')
                ->nullable()
                ->comment("Данные Куратра/менеджера/админа кто открый заявку");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('user_applications', function (Blueprint $table) {
            $table->dropColumn('visitor');
        });
    }
};
