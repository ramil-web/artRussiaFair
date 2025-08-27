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
        Schema::create('admin_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')
                ->comment('Идентификатор события')
                ->constrained()
                ->onDelete('cascade');
            $table->string('name')
                ->comment('Название файла');
            $table->string('link')
                ->comment('ссылка для скачивание');
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
        Schema::dropIfExists('admin_documents');
    }
};
