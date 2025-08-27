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
        Schema::table('orders', function (Blueprint $table) {
            $table->bigInteger('time_slot_start_id')
                ->nullable()
                ->comment("порядковый номер слота");
            $table->timestamp('deleted_at')
                ->nullable();
        });

        if (!Schema::hasColumn('products','article')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('article')
                    ->nullable()
                    ->comment('Артикул товара');
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('time_slot_start_id');
            $table->dropColumn('deleted_at');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('article');
        });
    }
};
