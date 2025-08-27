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
        //TODO::GYULALYEV::здесь вопрос по поводу каскандного удаления
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->foreignId('category_product_id')->nullable()
                ->constrained();
//                ->onDelete('cascade');
            $table->json('description');
            $table->json('specifications');
            $table->integer('price');
            $table->softDeletes();
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
        Schema::dropIfExists('products');
    }
};
