<?php

declare(strict_types=1);

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
        Schema::create('attendance_sticker_print_settings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->decimal('width_mm', 6, 2);
            $table->decimal('height_mm', 6, 2);
            $table->decimal('margin_top_mm', 6, 2);
            $table->decimal('margin_right_mm', 6, 2);
            $table->decimal('margin_bottom_mm', 6, 2);
            $table->decimal('margin_left_mm', 6, 2);
            $table->timestamps();

            $table->unique(['user_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_sticker_print_settings');
    }
};
