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
        DB::statement("ALTER TABLE service_catalogs ALTER COLUMN other DROP NOT NULL;");
        Schema::table('service_catalogs', function (Blueprint $table) {
            $table->bigInteger('sort_id')
                ->default(1)
                ->comment('ID для костомной сортировки');
        });
        Schema::table('category_products', function (Blueprint $table) {
            $table->bigInteger('sort_id')
                ->default(1)
                ->comment('ID для костомной сортировки');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->bigInteger('sort_id')
                ->default(1)
                ->comment('ID для костомной сортировки');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('service_catalogs', function (Blueprint $table) {
            $table->dropColumn('sort_id');
        });
        Schema::table('category_products', function (Blueprint $table) {
            $table->dropColumn('sort_id');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sort_id');
        });
    }
};
