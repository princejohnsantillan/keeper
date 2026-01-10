<?php

use App\Models\TermAcceptance;
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
        Schema::table('gatepasses', function (Blueprint $table) {
            $table->foreignIdFor(TermAcceptance::class)->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gatepasses', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(TermAcceptance::class);
        });
    }
};
