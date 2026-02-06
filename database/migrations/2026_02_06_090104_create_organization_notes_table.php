<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_notes', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->ulidMorphs('notable');
            $table->text('note');
            $table->timestamps();

            $table->unique(['organization_id', 'notable_type', 'notable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_notes');
    }
};
