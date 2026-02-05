<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->unique(['organization_id', 'name']);
        });

        Schema::create('taggables', function (Blueprint $table) {
            $table->foreignUlid('tag_id')->constrained()->cascadeOnDelete();

            $table->ulidMorphs('taggable');

            $table->unique(['tag_id', 'taggable_id', 'taggable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taggables');
        Schema::dropIfExists('tags');
    }
};
