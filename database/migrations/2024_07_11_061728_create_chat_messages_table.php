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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('chat_room_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->text("message")
                ->nullable()
                ->comment("Text of message");
            $table->string('file_path')
                ->nullable()
                ->comment("The path for file in storage");
            $table->string('file_name')
                ->nullable()
                ->comment("The  name of file");
            $table->boolean('status')
                ->nullable()
                ->comment("Status of message, read or not")
                ->default(false);
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
        Schema::dropIfExists('chat_messages');
    }
};
