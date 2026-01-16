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
        Schema::create('activities', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('title')->index();
            $table->string('description')->nullable();
            $table->string('location')->index();
            $table->string('location_map_link')->nullable();
            $table->timestamp('starts_at')->index();
            $table->timestamp('ends_at')->index();
            $table->timestamp('published_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignUlid('organization_id')->constrained();
            $table->foreignUlid('term_id')->nullable()->constrained();
            $table->foreignUlid('message_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
