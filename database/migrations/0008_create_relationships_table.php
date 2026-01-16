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
        Schema::create('relationships', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('guardian_id')->constrained();
            $table->foreignUlid('child_id')->constrained();
            $table->string('relationship')->index();
            $table->boolean('is_primary')->default(false)->index();
            $table->timestamps();

            $table->unique(['guardian_id', 'child_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relationships');
    }
};
