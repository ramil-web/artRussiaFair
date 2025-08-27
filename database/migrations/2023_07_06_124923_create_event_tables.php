<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('slug');
            $table->json('description');
            $table->json('place');
            $table->json('social_links');
            $table->string('type')->default('main');
            $table->year('year')->comment('год проведения');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('status')->default(true);
            $table->integer('order_column')->nullable();
            $table->timestamps();

        });

        Schema::create('eventgables', function (Blueprint $table) {
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();

            $table->morphs('eventgable');

            $table->unique(['event_id', 'eventgable_id', 'eventgable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventgables');
        Schema::dropIfExists('events');
    }
};
