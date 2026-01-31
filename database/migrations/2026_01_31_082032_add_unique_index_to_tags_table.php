<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // Case-insensitive unique index for PostgreSQL
            DB::statement('CREATE UNIQUE INDEX tags_organization_id_name_unique ON tags (organization_id, LOWER(name))');
        } else {
            // SQLite - use COLLATE NOCASE for case-insensitive comparison
            DB::statement('CREATE UNIQUE INDEX tags_organization_id_name_unique ON tags (organization_id, name COLLATE NOCASE)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags', function () {
            DB::statement('DROP INDEX IF EXISTS tags_organization_id_name_unique');
        });
    }
};
