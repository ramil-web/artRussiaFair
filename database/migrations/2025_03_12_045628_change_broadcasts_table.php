<?php

use App\Exceptions\CustomException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->string('barcode')
                ->nullable()
                ->comment('Баркоды котроы получаеть участник при покупки билета');
            $table->string('product_id')
                ->nullable()
                ->comment('ID билета');
            $table->boolean('codes_2025')->default(false);
        });

        Artisan::call('db:seed', [
            '--class' => 'BarCodeSeeder',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::table('broadcasts')->where('codes_2025', true)->delete();
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->dropColumn('barcode');
            $table->dropColumn('product_id');
            $table->dropColumn('codes_2025');
        });
    }

};
