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
        Schema::create('invitations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->foreignUlid('activity_id')->constrained()->cascadeOnDelete();
            $table->string('invitee_fullname');
            $table->string('invitee_email');
            $table->string('invitee_phone')->nullable();
            $table->foreignUlid('used_on_child_id')->nullable()->constrained('children')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignUlid('message_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('created_by')->constrained('users');
            $table->timestamps();

            $table->unique(['organization_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
