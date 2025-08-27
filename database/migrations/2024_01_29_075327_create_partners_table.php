<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sort_id');
            $table->foreignId('partner_category_id')
                ->comment('ID категории партнера')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');
            $table->json('name')
                ->comment('Имя партнера');
            $table->string('link')
                ->comment('Внешняя ссылка')
                ->nullable();
            $table->string('image')
                ->nullable()
                ->comment('Ссылка на изображение');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partners');
    }
};
