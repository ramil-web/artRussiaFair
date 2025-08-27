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
        Schema::create('user_applications', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->foreignId('user_id')->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('type');
            $table->json('name_gallery')->nullable();
            $table->json('representative_name');
            $table->json('representative_surname');
            $table->string('representative_email');
            $table->string('representative_phone');
            $table->json('representative_city');
            $table->json('about_style');
            $table->json('about_description');
            $table->json('other_fair')->nullable();
            $table->json('social_links')->nullable();
            $table->string('status')->default('new');
            $table->boolean('active')->default('true');
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
        Schema::dropIfExists('user_applications');
    }
};
