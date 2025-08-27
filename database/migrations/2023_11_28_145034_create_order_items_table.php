<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Grammar::macro('typeRaw', function (Fluent $column) {
            return $column->get('raw_type');
        });

        Blueprint::macro('addColumnRaw', function ($rawType, $name) {
            assert($this instanceof Blueprint);
            return $this->addColumn('raw', $name, ['raw_type' => $rawType]);
        });

        DB::unprepared("CREATE TYPE order_item_type AS ENUM ('hardware', 'additional_service');");


        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id')
                ->unsigned();
            $table->integer('quantity')
                ->default(1)
                ->comment("Количество");
            $table->addColumnRaw('order_item_type', 'type')
                ->nullable()
                ->comment('Тип,аренда, оборудование, доп услуги т. д.');
            $table->foreignId('order_id')
                ->constrained()
                ->onDelete('cascade');
            $table->bigInteger('product_id')
                ->comment('Идентификатор товара')
                ->nullable();
            $table->bigInteger('service_catalog_id')
                ->comment('Идентификатор товара')
                ->nullable();
            $table->timestamp('deleted_at')
                ->nullable();
            $table->timestamp('created_at')
                ->useCurrent();
            $table->timestamp('updated_at')
                ->useCurrent();
        });

        /**
         * Добавляем все статусы как костомный тип. Enum nип в Laravel не редактируется на прямую.
//         */
//        DB::statement("ALTER TABLE user_applications DROP CONSTRAINT user_applications_status_check");
//
//        $types = ['new', 'under consideration', 'revision', 'confirmed', 'rejected', 'under_consideration', 'waitin_after_edit', 'pre_assessment'];
//        $result = join( ', ', array_map(function ($value){
//            return sprintf("'%s'::character varying", $value);
//        }, $types));
//
//        DB::statement("ALTER TABLE user_applications ADD CONSTRAINT user_applications_status_check CHECK (status::text = ANY (ARRAY[$result]::text[]))");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
        DB::unprepared("DROP TYPE IF EXISTS user_applications_status CASCADE");
        DB::unprepared("DROP TYPE order_item_type");
    }
};
