<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('organization_notes')->delete();

        Schema::dropIfExists('taggables');
        Schema::dropIfExists('tags');

        Schema::create('organization_tags', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->ulidMorphs('taggable');
            $table->string('name');
            $table->timestamps();

            $table->unique([
                'organization_id',
                'taggable_type',
                'taggable_id',
                'name',
            ], 'organization_tags_org_taggable_name_unique');

            $table->index(
                ['organization_id', 'name'],
                'organization_tags_organization_id_name_index',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_tags');

        Schema::create('tags', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->unique(['organization_id', 'name']);
        });

        Schema::create('taggables', function (Blueprint $table): void {
            $table->foreignUlid('tag_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->ulidMorphs('taggable');

            $table->unique(['tag_id', 'taggable_id', 'taggable_type']);
        });
    }
};
