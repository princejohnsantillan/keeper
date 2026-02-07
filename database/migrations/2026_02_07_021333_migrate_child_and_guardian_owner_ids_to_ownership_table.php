<?php

declare(strict_types=1);

use App\Models\Child;
use App\Models\Guardian;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $userMorphClass = (new User)->getMorphClass();
        $childMorphClass = (new Child)->getMorphClass();
        $guardianMorphClass = (new Guardian)->getMorphClass();

        $this->deduplicateOwnershipRows();

        Schema::table('ownership', function (Blueprint $table) {
            $table->unique([
                'owner_type',
                'owner_id',
                'model_type',
                'model_id',
            ], 'ownership_owner_model_unique');
        });

        $this->backfillOwnership('children', $childMorphClass, $userMorphClass);
        $this->backfillOwnership('guardians', $guardianMorphClass, $userMorphClass);

        $this->assertBackfillIntegrity($userMorphClass, $childMorphClass, $guardianMorphClass);

        Schema::table('children', function (Blueprint $table): void {
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
        });

        Schema::table('guardians', function (Blueprint $table): void {
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('This migration is irreversible. Restore from a database backup.');
    }

    private function deduplicateOwnershipRows(): void
    {
        $duplicateGroups = DB::table('ownership')
            ->select(['owner_type', 'owner_id', 'model_type', 'model_id'])
            ->selectRaw('COUNT(*) AS duplicate_count')
            ->groupBy('owner_type', 'owner_id', 'model_type', 'model_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicateGroups as $duplicateGroup) {
            $duplicateIds = DB::table('ownership')
                ->where('owner_type', $duplicateGroup->owner_type)
                ->where('owner_id', $duplicateGroup->owner_id)
                ->where('model_type', $duplicateGroup->model_type)
                ->where('model_id', $duplicateGroup->model_id)
                ->orderBy('created_at')
                ->orderBy('id')
                ->pluck('id');

            if ($duplicateIds->count() <= 1) {
                continue;
            }

            DB::table('ownership')
                ->whereIn('id', $duplicateIds->slice(1)->all())
                ->delete();
        }
    }

    private function backfillOwnership(string $table, string $modelMorphClass, string $userMorphClass): void
    {
        DB::table($table)
            ->select(['id', 'owner_id'])
            ->whereNotNull('owner_id')
            ->orderBy('id')
            ->chunk(500, function (Collection $rows) use ($modelMorphClass, $userMorphClass): void {
                $timestamp = now();

                $ownershipRows = $rows
                    ->map(fn (object $row): array => [
                        'id' => (string) Str::ulid(),
                        'owner_type' => $userMorphClass,
                        'owner_id' => $row->owner_id,
                        'model_type' => $modelMorphClass,
                        'model_id' => $row->id,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ])
                    ->all();

                DB::table('ownership')->insertOrIgnore($ownershipRows);
            });
    }

    private function assertBackfillIntegrity(
        string $userMorphClass,
        string $childMorphClass,
        string $guardianMorphClass,
    ): void {
        $missingChildrenCount = DB::table('children')
            ->leftJoin('ownership', function (JoinClause $join) use ($userMorphClass, $childMorphClass): void {
                $join->on('ownership.model_id', '=', 'children.id')
                    ->on('ownership.owner_id', '=', 'children.owner_id')
                    ->where('ownership.model_type', '=', $childMorphClass)
                    ->where('ownership.owner_type', '=', $userMorphClass);
            })
            ->whereNotNull('children.owner_id')
            ->whereNull('ownership.id')
            ->count();

        if ($missingChildrenCount > 0) {
            throw new RuntimeException('Ownership backfill failed for one or more children records.');
        }

        $missingGuardiansCount = DB::table('guardians')
            ->leftJoin('ownership', function (JoinClause $join) use ($userMorphClass, $guardianMorphClass): void {
                $join->on('ownership.model_id', '=', 'guardians.id')
                    ->on('ownership.owner_id', '=', 'guardians.owner_id')
                    ->where('ownership.model_type', '=', $guardianMorphClass)
                    ->where('ownership.owner_type', '=', $userMorphClass);
            })
            ->whereNotNull('guardians.owner_id')
            ->whereNull('ownership.id')
            ->count();

        if ($missingGuardiansCount > 0) {
            throw new RuntimeException('Ownership backfill failed for one or more guardian records.');
        }
    }
};
