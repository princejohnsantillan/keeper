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
                target_table text;
                id_sequence regclass;
                maximum_id bigint;
                sequence_last_value bigint;
                sequence_is_called boolean;
            BEGIN
                FOREACH target_table IN ARRAY ARRAY[
                    'migrations', 'media', 'exports', 'failed_jobs',
                    'imports', 'failed_import_rows', 'jobs'
                ] LOOP
                    EXECUTE format('LOCK TABLE %I IN SHARE ROW EXCLUSIVE MODE', target_table);

                    id_sequence := pg_get_serial_sequence(target_table, 'id')::regclass;

                    IF id_sequence IS NULL THEN
                        SELECT dependency.refobjid::regclass INTO STRICT id_sequence
                        FROM pg_attribute AS attribute
                        JOIN pg_attrdef AS attribute_default
                            ON attribute_default.adrelid = attribute.attrelid
                            AND attribute_default.adnum = attribute.attnum
                        JOIN pg_depend AS dependency
                            ON dependency.classid = 'pg_attrdef'::regclass
                            AND dependency.objid = attribute_default.oid
                            AND dependency.refclassid = 'pg_class'::regclass
                            AND dependency.deptype = 'n'
                        JOIN pg_class AS sequence
                            ON sequence.oid = dependency.refobjid
                            AND sequence.relkind = 'S'
                        WHERE attribute.attrelid = target_table::regclass
                            AND attribute.attname = 'id'
                            AND NOT attribute.attisdropped;
                    END IF;

                    EXECUTE format('SELECT MAX(id) FROM %I', target_table) INTO maximum_id;

                    IF maximum_id IS NULL THEN
                        CONTINUE;
                    END IF;

                    EXECUTE format('SELECT last_value, is_called FROM %s', id_sequence)
                        INTO sequence_last_value, sequence_is_called;

                    IF maximum_id > sequence_last_value
                        OR (maximum_id = sequence_last_value AND NOT sequence_is_called) THEN
                        EXECUTE format('ALTER SEQUENCE %s RESTART WITH %s', id_sequence, maximum_id + 1);
                    END IF;
                END LOOP;
            END
            $$;
            SQL);
    }

    /**
     * Keep repaired sequences on rollback to avoid reusing existing IDs.
     */
    public function down(): void {}
};
