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
        Schema::create('requisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('my_document_id')->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string("payment_account")
                ->comment("Расчётный счет");
            $table->string("bank_name")
                ->comment("Наименование банка");
            $table->string("bic")
                ->comment("БИК");
            $table->string("correspondent_account")
                ->comment("Корреспондентский счет");
            $table->string("kpp")
                ->comment("КПП");
            $table->string("inn")
                ->comment("ИНН");
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
        Schema::dropIfExists('requisites');
    }
};
