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
        Schema::create('classic_user_applications', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->foreignId('user_id')->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('type');
            $table->jsonb('name_gallery')->nullable();
            $table->jsonb('representative_name');
            $table->jsonb('representative_surname');
            $table->string('representative_email');
            $table->string('representative_phone');
            $table->jsonb('representative_city');
            $table->jsonb('about_style');
            $table->jsonb('about_description');
            $table->jsonb('other_fair')->nullable();
            $table->jsonb('social_links')->nullable();
            $table->string('status')->default('new');
            $table->boolean('active')->default('true');
            $table->foreignId('event_id')->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->jsonb('visitor')
                ->nullable()
                ->comment("Данные Куратра/менеджера/админа кто открый заявку");
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
        Schema::dropIfExists('classic_user_applications');
    }
};
