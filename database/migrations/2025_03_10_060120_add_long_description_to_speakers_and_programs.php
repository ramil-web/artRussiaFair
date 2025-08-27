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
        Schema::table('speakers', function (Blueprint $table) {
            $table->json('full_description')
                ->comment('Полное описание')
                ->nullable();
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->json('description')
                ->comment('Описание')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('speakers', function (Blueprint $table) {
            $table->dropColumn('full_description');
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
