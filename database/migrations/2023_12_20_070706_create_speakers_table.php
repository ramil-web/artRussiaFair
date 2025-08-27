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

        Blueprint::macro('speakerType', function ($rawType, $name) {
            assert($this instanceof Blueprint);
            return $this->addColumn('raw', $name, ['raw_type' => $rawType]);
        });

        DB::unprepared("CREATE TYPE speaker_type AS ENUM ('speaker', 'project_team', 'curator');");


        Schema::create('speakers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sort_id')
                ->comment('Номер для сортировки');
            $table->json('name')
            ->comment('Имя спикера/члена команды');
            $table->json('description')
            ->comment('Краткое описание');
            $table->speakerType('speaker_type', 'type')
                ->comment('Тип,спикеры, член команды');
            $table->string('url')
                ->nullable()
                ->comment('Ссылка на изображение');

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
        Schema::dropIfExists("speakers");
        DB::unprepared("DROP TYPE speaker_type");
    }
};
