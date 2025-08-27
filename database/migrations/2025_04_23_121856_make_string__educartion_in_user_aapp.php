<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE user_applications ALTER COLUMN education TYPE varchar USING education::text;");
    }

    /**
     * @return void
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE user_applications ALTER COLUMN education TYPE jsonb USING education::jsonb;");
    }
};
