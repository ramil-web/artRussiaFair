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
        Schema::table('information_for_placements', function (Blueprint $table) {
            $table->json('url')->nullable()->change();
            $table->json('name')->nullable()->change();
            $table->json('description')->nullable()->change();
            $table->json('social_network')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('information_for_placements', function (Blueprint $table) {
            $table->json('url')->nullable(false)->change();
            $table->json('name')->nullable(false)->change();
            $table->json('description')->nullable(false)->change();
            $table->dropColumn('social_network');
        });
    }
};
