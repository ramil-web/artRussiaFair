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
        Schema::create('information_for_placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_application_id')->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->json('name');
            $table->json('description');
            $table->json('url');
            $table->softDeletes();
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
        Schema::dropIfExists('information_for_placements');
    }
};
