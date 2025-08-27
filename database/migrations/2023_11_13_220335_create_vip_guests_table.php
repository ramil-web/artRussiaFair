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
        Schema::create('vip_guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_application_id')->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->json('full_name');
            $table->json('organization');
            $table->string('email');
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
        Schema::dropIfExists('vip_guests');
    }
};
