<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Activity;
use App\Models\Attendance;
use App\Models\OrganizationTag;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

final class GetActivityTagCountsAction
{
    /**
     * Counts, per organization tag, how many children registered to the
     * activity carry the tag and how many of those actually checked in.
     *
     * Only tags belonging to the activity's organization are counted, since a
     * child may be tagged by several organizations.
     *
     * @param  Collection<int, Attendance>|null  $attendances  Preloaded attendances with `child.organizationTags`; loaded from the activity when omitted.
     * @return array<string, array{registered: int, attended: int}> Keyed by tag name, ordered by attended then registered, descending.
     */
    public function __invoke(Activity $activity, ?Collection $attendances = null): array
    {
        $attendances ??= $activity->attendance()
            ->with([
                'child' => fn (BelongsTo $q): BelongsTo => $q->withTrashed()->with('organizationTags'),
            ])
            ->get();

        $organizationId = $activity->organization_id;

        return $attendances
            ->flatMap(fn (Attendance $attendance): Collection => ($attendance->child?->organizationTags ?? collect())
                ->where('organization_id', $organizationId)
                ->map(fn (OrganizationTag $tag): array => [
                    'name' => $tag->name,
                    'attended' => $attendance->checked_in_at !== null,
                ]))
            ->groupBy('name')
            ->map(fn (Collection $rows): array => [
                'registered' => $rows->count(),
                'attended' => $rows->where('attended', true)->count(),
            ])
            ->sortBy([
                ['attended', 'desc'],
                ['registered', 'desc'],
            ])
            ->all();
    }
}
