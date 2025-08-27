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
        Schema::create('classic_user_application_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classic_user_application_id')->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('url');
            $table->string('title');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('classic_user_application_images');
    }
};
