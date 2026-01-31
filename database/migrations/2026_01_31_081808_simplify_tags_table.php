<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First drop the index that references the type column
        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex('tags_organization_id_type_index');
        });

        // Add temp column for name
        Schema::table('tags', function (Blueprint $table) {
            $table->string('name_temp')->nullable();
        });

        // Extract the English name from the JSON (PostgreSQL syntax)
        if (DB::getDriverName() === 'pgsql') {
            DB::table('tags')->update([
                'name_temp' => DB::raw("name->>'en'"),
            ]);
        } else {
            // SQLite syntax
            DB::table('tags')->update([
                'name_temp' => DB::raw("json_extract(name, '$.en')"),
            ]);
        }

        // Drop old columns
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn(['name', 'slug', 'type', 'order_column']);
        });

        // Rename temp to name
        Schema::table('tags', function (Blueprint $table) {
            $table->renameColumn('name_temp', 'name');
        });

        // Add index on organization_id
        Schema::table('tags', function (Blueprint $table) {
            $table->index('organization_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex(['organization_id']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->json('name_json')->nullable();
            $table->json('slug')->nullable();
            $table->string('type')->nullable();
            $table->integer('order_column')->nullable();
        });

        // Convert string back to JSON
        if (DB::getDriverName() === 'pgsql') {
            DB::table('tags')->update([
                'name_json' => DB::raw("jsonb_build_object('en', name)"),
            ]);
        } else {
            DB::table('tags')->update([
                'name_json' => DB::raw("json_object('en', name)"),
            ]);
        }

        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->renameColumn('name_json', 'name');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->index(['organization_id', 'type'], 'tags_organization_id_type_index');
        });
    }
};
