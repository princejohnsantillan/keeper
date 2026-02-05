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
        Schema::create('attendances', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('activity_id')->constrained();
            $table->foreignUlid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('child_id')->constrained();

            $table->foreignUlid('checkin_keeper_id')->nullable()->constrained('keepers');
            $table->foreignUlid('checkin_gatepass_id')->nullable()->constrained('gatepasses');
            $table->timestamp('checked_in_at')->nullable();

            $table->foreignUlid('checkout_keeper_id')->nullable()->constrained('keepers');
            $table->foreignUlid('checkout_gatepass_id')->nullable()->constrained('gatepasses');
            $table->timestamp('checked_out_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps(); // created_at is when the child is registered for the service
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
