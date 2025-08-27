<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;

return new class extends Migration
{
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

        Blueprint::macro('participantType', function ($rawType, $name) {
            assert($this instanceof Blueprint);
            return $this->addColumn('raw', $name, ['raw_type' => $rawType]);
        });

        DB::unprepared("CREATE TYPE participant_type AS ENUM ('artist', 'sculptor', 'photographer');");


        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sort_id')
                ->comment('Номер для сортировки');
            $table->json('name')
                ->comment('Имя художника/скульптора/фотографа');
            $table->json('description')
                ->comment('Краткое описание');
            $table->participantType('participant_type', 'type')
                ->comment('Тип,художника/скульптора/фотографа');
            $table->string('image')
                ->nullable()
                ->comment('Ссылка на изображение');
            $table->string('stand_id')
                ->comment('Номер стенда')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("participants");
        DB::unprepared("DROP TYPE participant_type");
    }
};
