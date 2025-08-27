<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSitePersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_persons', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('section');
            $table->string('slug');
            $table->string('name');
            $table->text('description');
            $table->string('place')->nullable();
            $table->string('image')->nullable();
            $table->unsignedTinyInteger('sort');
            $table->boolean('published')->default(true);
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
        Schema::dropIfExists('site_persons');
    }
}
