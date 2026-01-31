<?php

use App\Models\Gatepass;
use App\ReadableCode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, handle any existing duplicate codes by regenerating them
        $this->regenerateDuplicateCodes();

        Schema::table('gatepasses', function (Blueprint $table) {
            // Drop the composite unique index
            $table->dropUnique(['code', 'activity_id']);

            // Add unique index on code alone
            $table->unique('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gatepasses', function (Blueprint $table) {
            // Drop the unique index on code
            $table->dropUnique(['code']);

            // Restore the composite unique index
            $table->unique(['code', 'activity_id']);
        });
    }

    /**
     * Find and regenerate any duplicate codes across different activities.
     */
    private function regenerateDuplicateCodes(): void
    {
        // Find all codes that appear more than once
        $duplicateCodes = DB::table('gatepasses')
            ->select('code')
            ->groupBy('code')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('code');

        if ($duplicateCodes->isEmpty()) {
            return;
        }

        // For each duplicate code, keep the first one and regenerate the rest
        foreach ($duplicateCodes as $duplicateCode) {
            $gatepasses = Gatepass::query()
                ->where('code', $duplicateCode)
                ->orderBy('created_at')
                ->get();

            // Skip the first one (keep its code), regenerate the rest
            foreach ($gatepasses->skip(1) as $gatepass) {
                $gatepass->update([
                    'code' => $this->generateUniqueCode(),
                ]);
            }
        }
    }

    /**
     * Generate a globally unique code.
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = ReadableCode::generate();
        } while (Gatepass::query()->where('code', $code)->exists());

        return $code;
    }
};
