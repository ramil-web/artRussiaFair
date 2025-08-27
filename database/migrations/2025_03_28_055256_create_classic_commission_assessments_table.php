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
        Schema::create('classic_commission_assessments', function (Blueprint $table) {
            $table->id();
            $table->string('status')
                ->comment('Статаус заявки, это сататус от комисси, и может отличаться от статуса заявки');
            $table->string('comment', 500)
                ->comment('Коментарий от комисси');
            $table->foreignId('classic_user_application_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->softDeletes();
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
        Schema::dropIfExists('classic_commission_assessments');
    }
};
