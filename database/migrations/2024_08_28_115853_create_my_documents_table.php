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
        Schema::create('my_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_application_id')->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('status')
                ->comment("Физлицо/юрлицо/самозанятый");
            $table->json("files")
                ->comment("Название, тип и адрес где хронится файлы");
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
        Schema::dropIfExists('my_documents');
    }
};
