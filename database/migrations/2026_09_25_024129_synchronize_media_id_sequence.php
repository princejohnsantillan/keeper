<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared(<<<'SQL'
            DO $$
            DECLARE
                media_sequence regclass;
                maximum_media_id bigint;
                sequence_last_value bigint;
                sequence_is_called boolean;
            BEGIN
                LOCK TABLE media IN SHARE ROW EXCLUSIVE MODE;

                media_sequence := pg_get_serial_sequence('media', 'id')::regclass;

                IF media_sequence IS NULL THEN
                    RAISE EXCEPTION 'Cannot synchronize media.id: no owned sequence found';
                END IF;

                SELECT MAX(id) INTO maximum_media_id FROM media;

                IF maximum_media_id IS NULL THEN
                    RETURN;
                END IF;

                EXECUTE format('SELECT last_value, is_called FROM %s', media_sequence)
                    INTO sequence_last_value, sequence_is_called;

                IF maximum_media_id > sequence_last_value
                    OR (maximum_media_id = sequence_last_value AND NOT sequence_is_called) THEN
                    EXECUTE format('ALTER SEQUENCE %s RESTART WITH %s', media_sequence, maximum_media_id + 1);
                END IF;
            END
            $$;
            SQL);
    }

    /**
     * Keep the repaired sequence on rollback to avoid reusing existing media IDs.
     */
    public function down(): void {}
};
