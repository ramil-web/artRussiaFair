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
        Schema::create('classic_events', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug');
            $table->json('description');
            $table->json('place');
            $table->json('social_links');
            $table->string('event_type')->default('main');
            $table->year('year')->comment('год проведения');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('status')->default(true);
            $table->bigInteger('sort_id')
                ->default(1)
                ->comment('ID для костомной сортировки');
            $table->dateTime('start_accepting_applications')
                ->nullable()
                ->comment("The beginning of accepting applications");
            $table->dateTime('end_accepting_applications')
                ->nullable()
                ->comment("The end of accepting applications");
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
        Schema::dropIfExists('classic_events');
    }
};
