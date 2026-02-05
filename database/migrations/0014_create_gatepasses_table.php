<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gatepasses', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('guardian_id')->constrained();
            $table->foreignUlid('child_id')->constrained();
            $table->foreignUlid('activity_id')->constrained();
            $table->foreignUlid('term_acceptance_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->timestamps();

            $table->unique('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gatepasses');
    }
};
