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
        $codes = array(
            array('barcode' => '838977605979', 'product_id' => '271102839'),
            array('barcode' => '987759604386', 'product_id' => '271102839'),
            array('barcode' => '316910632342', 'product_id' => '271102839'),
            array('barcode' => '331742177840', 'product_id' => '271102839'),
            array('barcode' => '751182290679', 'product_id' => '271102839'),
            array('barcode' => '687060525163', 'product_id' => '271102839'),
            array('barcode' => '193785964784', 'product_id' => '271102839'),
            array('barcode' => '698665207250', 'product_id' => '271102839'),
            array('barcode' => '330841857503', 'product_id' => '271102839'),
            array('barcode' => '898285030106', 'product_id' => '271102839'),
            array('barcode' => '890115457298', 'product_id' => '271102839'),
            array('barcode' => '927905020516', 'product_id' => '271102839'),
            array('barcode' => '219432876929', 'product_id' => '271102839'),
            array('barcode' => '818356079811', 'product_id' => '271102839'),
            array('barcode' => '674146226862', 'product_id' => '271102839'),
            array('barcode' => '343850194889', 'product_id' => '271102839'),
            array('barcode' => '466114263950', 'product_id' => '271102839'),
            array('barcode' => '361795863846', 'product_id' => '271102839'),
            array('barcode' => '179369640331', 'product_id' => '271102839'),
            array('barcode' => '552136994481', 'product_id' => '271102839'),
            array('barcode' => '976552734967', 'product_id' => '271102839'),
            array('barcode' => '485545616862', 'product_id' => '271102839'),
            array('barcode' => '936668708488', 'product_id' => '271102839'),
            array('barcode' => '227071512329', 'product_id' => '271102839'),
            array('barcode' => '184616960663', 'product_id' => '271102839'),
            array('barcode' => '864355244454', 'product_id' => '271102839'),
            array('barcode' => '161473601461', 'product_id' => '271102839'),
            array('barcode' => '989077830490', 'product_id' => '271102839'),
            array('barcode' => '739492968972', 'product_id' => '271102839'),
            array('barcode' => '548942538862', 'product_id' => '271102839'),
            array('barcode' => '474748120433', 'product_id' => '271102839'),
            array('barcode' => '480008041281', 'product_id' => '271102839'),
            array('barcode' => '547696303248', 'product_id' => '271102839'),
            array('barcode' => '786433992987', 'product_id' => '271102839'),
            array('barcode' => '113255740342', 'product_id' => '271102839'),
            array('barcode' => '874229732411', 'product_id' => '271102839'),
            array('barcode' => '491339855539', 'product_id' => '271102839'),
            array('barcode' => '667666266362', 'product_id' => '271102839'),
            array('barcode' => '663876963838', 'product_id' => '271102839'),
            array('barcode' => '804942518477', 'product_id' => '271102839'),
            array('barcode' => '219291779323', 'product_id' => '271102839'),
            array('barcode' => '922337743660', 'product_id' => '271102839'),
            array('barcode' => '951067718445', 'product_id' => '271102839'),
            array('barcode' => '369523560320', 'product_id' => '271102839'),
            array('barcode' => '461614782886', 'product_id' => '271102839'),
            array('barcode' => '545245434758', 'product_id' => '271102839'),
            array('barcode' => '705406817240', 'product_id' => '271102839'),
            array('barcode' => '995797217540', 'product_id' => '271102839'),
            array('barcode' => '971762380908', 'product_id' => '271102839'),
            array('barcode' => '701586882212', 'product_id' => '271102839')
        );

        $data =  [];
        foreach ($codes as $code) {
            $data[] = [
                'barcode'    => $code['barcode'],
                'product_id' => $code['product_id'],
                'codes_2025' => true,
                'created_at' => now()
            ];
        }
        DB::table('broadcasts')
            ->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            //
        });
    }
};
