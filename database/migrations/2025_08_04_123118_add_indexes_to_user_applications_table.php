<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Индексы на таблицу user_applications
        $this->createIndexIfNotExists('user_applications', 'user_id');
        $this->createIndexIfNotExists('user_applications', 'status');
        $this->createIndexIfNotExists('user_applications', 'type');
        $this->createIndexIfNotExists('user_applications', 'representative_email');
        $this->createIndexIfNotExists('user_applications', 'event_id');
        $this->createIndexIfNotExists('user_applications', 'active');
        $this->createIndexIfNotExists('user_applications', 'deleted_at');
        $this->createIndexIfNotExists('user_applications', 'created_at');

        // Составной индекс
        DB::statement("
            DO $$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_indexes WHERE indexname = 'idx_user_app_event_del_created'
                ) THEN
                    CREATE INDEX idx_user_app_event_del_created
                        ON user_applications (event_id, deleted_at, created_at);
                END IF;
            END$$;
        ");

        // JSONB индексы
        DB::statement("
            DO $$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_indexes WHERE indexname = 'idx_user_app_name_gallery_ru'
                ) THEN
                    CREATE INDEX idx_user_app_name_gallery_ru
                        ON user_applications ((name_gallery->>'ru'));
                END IF;
            END$$;
        ");
        DB::statement("
            DO $$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_indexes WHERE indexname = 'idx_user_app_city_ru'
                ) THEN
                    CREATE INDEX idx_user_app_city_ru
                        ON user_applications ((representative_city->>'ru'));
                END IF;
            END$$;
        ");
        DB::statement("
            DO $$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_indexes WHERE indexname = 'idx_user_app_surname_ru'
                ) THEN
                    CREATE INDEX idx_user_app_surname_ru
                        ON user_applications ((representative_surname->>'ru'));
                END IF;
            END$$;
        ");

        // Индекс на visualizations
        DB::statement("
            DO $$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_indexes WHERE indexname = 'idx_visualizations_user_app_del'
                ) THEN
                    CREATE INDEX idx_visualizations_user_app_del
                        ON visualizations (user_application_id, deleted_at);
                END IF;
            END$$;
        ");

        // Индекс на events
        DB::statement("
            DO $$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_indexes WHERE indexname = 'idx_events_category_del'
                ) THEN
                    CREATE INDEX idx_events_category_del
                        ON events (category, deleted_at);
                END IF;
            END$$;
        ");
    }

    public function down(): void
    {
        $this->dropIndexIfExists('user_applications', 'user_id');
        $this->dropIndexIfExists('user_applications', 'status');
        $this->dropIndexIfExists('user_applications', 'type');
        $this->dropIndexIfExists('user_applications', 'representative_email');
        $this->dropIndexIfExists('user_applications', 'event_id');
        $this->dropIndexIfExists('user_applications', 'active');
        $this->dropIndexIfExists('user_applications', 'deleted_at');
        $this->dropIndexIfExists('user_applications', 'created_at');
        $this->dropIndexIfExists('user_applications', 'idx_user_app_event_del_created');

        DB::statement("DROP INDEX IF EXISTS idx_user_app_name_gallery_ru");
        DB::statement("DROP INDEX IF EXISTS idx_user_app_city_ru");
        DB::statement("DROP INDEX IF EXISTS idx_user_app_surname_ru");

        DB::statement("DROP INDEX IF EXISTS idx_visualizations_user_app_del");
        DB::statement("DROP INDEX IF EXISTS idx_events_category_del");
    }

    private function createIndexIfNotExists(string $table, string $column): void
    {
        $index = "{$table}_{$column}_index";
        DB::statement("
            DO $$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_indexes WHERE indexname = '{$index}'
                ) THEN
                    CREATE INDEX {$index} ON {$table} ({$column});
                END IF;
            END$$;
        ");
    }

    private function dropIndexIfExists(string $table, string $columnOrName): void
    {
        $index = str_starts_with($columnOrName, 'idx_') ? $columnOrName : "{$table}_{$columnOrName}_index";
        DB::statement("DROP INDEX IF EXISTS {$index}");
    }
};
