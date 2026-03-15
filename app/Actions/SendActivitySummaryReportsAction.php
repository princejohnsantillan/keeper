<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Gender;
use App\Enums\KeeperRole;
use App\Enums\KeeperStatus;
use App\Mail\ActivitySummaryReportMail;
use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

final class SendActivitySummaryReportsAction
{
    public function __invoke(): int
    {
        $cutoff = now()->subHour();
        $queuedEmails = 0;

        $activities = Activity::query()
            ->withoutGlobalScope(OrganizationScope::class)
            ->whereNull('summary_report_sent_at')
            ->where('ends_at', '<=', $cutoff)
            ->where('ends_at', '>=', now()->subWeek())
            ->with([
                'organization.keepers' => fn (HasMany $q): HasMany => $q
                    ->where('role', KeeperRole::Admin)
                    ->where('status', KeeperStatus::Active)
                    ->with('user'),
                'attendance' => fn (HasMany $q): HasMany => $q->with([
                    'child' => fn (BelongsTo $q): BelongsTo => $q->withTrashed()->with('organizationTags'),
                    'checkinKeeper' => fn (BelongsTo $q): BelongsTo => $q->withTrashed()->with('user'),
                    'checkoutKeeper' => fn (BelongsTo $q): BelongsTo => $q->withTrashed()->with('user'),
                    'checkinGatepass' => fn (BelongsTo $q): BelongsTo => $q->with([
                        'guardian' => fn (BelongsTo $q): BelongsTo => $q->withTrashed(),
                    ]),
                    'checkoutGatepass' => fn (BelongsTo $q): BelongsTo => $q->with([
                        'guardian' => fn (BelongsTo $q): BelongsTo => $q->withTrashed(),
                    ]),
                ]),
            ])
            ->get();

        foreach ($activities as $activity) {
            $attendances = $activity->attendance;
            $checkedIn = $attendances->whereNotNull('checked_in_at');
            $checkedOut = $attendances->whereNotNull('checked_out_at');
            $noShows = $attendances->whereNull('checked_in_at');
            $incompleteCheckouts = $checkedIn->whereNull('checked_out_at');

            $stats = $this->computeStats($activity, $attendances, $checkedIn, $checkedOut, $noShows, $incompleteCheckouts);
            $csvContent = $this->generateCsv($attendances);

            $adminKeepers = $activity->organization?->keepers ?? collect();

            foreach ($adminKeepers as $keeper) {
                $email = $keeper->user?->email;

                if ($email === null || trim($email) === '') {
                    continue;
                }

                Mail::to($email)->queue(new ActivitySummaryReportMail($activity, $stats, $csvContent));

                $queuedEmails++;
            }

            $activity->forceFill(['summary_report_sent_at' => now()])->save();
        }

        return $queuedEmails;
    }

    /**
     * @param  Collection<int, Attendance>  $attendances
     * @param  Collection<int, Attendance>  $checkedIn
     * @param  Collection<int, Attendance>  $checkedOut
     * @param  Collection<int, Attendance>  $noShows
     * @param  Collection<int, Attendance>  $incompleteCheckouts
     * @return array<string, mixed>
     */
    private function computeStats(
        Activity $activity,
        Collection $attendances,
        Collection $checkedIn,
        Collection $checkedOut,
        Collection $noShows,
        Collection $incompleteCheckouts,
    ): array {
        $completeRecords = $checkedIn->whereNotNull('checked_out_at');
        $averageStayMinutes = $completeRecords->isNotEmpty()
            ? (int) round($completeRecords->avg(fn (Attendance $a): float => (float) $a->checked_in_at->diffInMinutes($a->checked_out_at)))
            : null;

        $firstCheckin = $checkedIn->sortBy('checked_in_at')->first();
        $lastCheckin = $checkedIn->sortByDesc('checked_in_at')->first();
        $firstCheckout = $checkedOut->sortBy('checked_out_at')->first();
        $lastCheckout = $checkedOut->sortByDesc('checked_out_at')->first();

        $organizationId = $activity->organization_id;
        $tagCounts = $checkedIn
            ->flatMap(fn (Attendance $a): Collection => $a->child?->organizationTags ?? collect())
            ->where('organization_id', $organizationId)
            ->countBy('name')
            ->sortDesc()
            ->all();

        $uniqueGuardianIds = $attendances
            ->pluck('checkinGatepass.guardian_id')
            ->merge($attendances->pluck('checkoutGatepass.guardian_id'))
            ->filter()
            ->unique()
            ->count();

        $attendedChildren = $checkedIn->map(fn (Attendance $a): array => [
            'childName' => $a->child?->full_name ?? 'Unknown',
            'checkedInAt' => $a->checked_in_at,
            'checkedOutAt' => $a->checked_out_at,
        ])->values()->all();

        $noShowChildren = $noShows->map(fn (Attendance $a): array => [
            'childName' => $a->child?->full_name ?? 'Unknown',
        ])->values()->all();

        $incompleteCheckoutChildren = $incompleteCheckouts->map(fn (Attendance $a): array => [
            'childName' => $a->child?->full_name ?? 'Unknown',
            'checkedInAt' => $a->checked_in_at,
        ])->values()->all();

        return [
            'activityTitle' => $activity->title,
            'activityLocation' => $activity->location,
            'activityStartsAt' => $activity->starts_at,
            'activityEndsAt' => $activity->ends_at,
            'totalRegistered' => $attendances->count(),
            'totalAttended' => $checkedIn->count(),
            'boysCount' => $checkedIn->filter(fn (Attendance $a): bool => $a->child?->gender === Gender::Male)->count(),
            'girlsCount' => $checkedIn->filter(fn (Attendance $a): bool => $a->child?->gender === Gender::Female)->count(),
            'averageStayMinutes' => $averageStayMinutes,
            'firstCheckin' => $firstCheckin ? ['childName' => $firstCheckin->child?->full_name ?? 'Unknown', 'time' => $firstCheckin->checked_in_at] : null,
            'lastCheckin' => $lastCheckin ? ['childName' => $lastCheckin->child?->full_name ?? 'Unknown', 'time' => $lastCheckin->checked_in_at] : null,
            'firstCheckout' => $firstCheckout ? ['childName' => $firstCheckout->child?->full_name ?? 'Unknown', 'time' => $firstCheckout->checked_out_at] : null,
            'lastCheckout' => $lastCheckout ? ['childName' => $lastCheckout->child?->full_name ?? 'Unknown', 'time' => $lastCheckout->checked_out_at] : null,
            'tagCounts' => $tagCounts,
            'uniqueGuardians' => $uniqueGuardianIds,
            'noShowsCount' => $noShows->count(),
            'incompleteCheckoutsCount' => $incompleteCheckouts->count(),
            'attendedChildren' => $attendedChildren,
            'noShowChildren' => $noShowChildren,
            'incompleteCheckoutChildren' => $incompleteCheckoutChildren,
        ];
    }

    /**
     * @param  Collection<int, Attendance>  $attendances
     */
    private function generateCsv(Collection $attendances): string
    {
        $timezone = config('app.display_timezone');

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, [
            'Child Name',
            'Check-in Gatepass',
            'Check-in Guardian',
            'Check-in Keeper',
            'Check-in Time',
            'Check-out Gatepass',
            'Check-out Guardian',
            'Check-out Keeper',
            'Check-out Time',
            'Duration (minutes)',
        ], escape: '\\');

        foreach ($attendances as $attendance) {
            $checkedInAt = $attendance->checked_in_at;
            $checkedOutAt = $attendance->checked_out_at;

            $duration = ($checkedInAt !== null && $checkedOutAt !== null)
                ? (string) max(1, (int) ceil($checkedInAt->diffInMinutes($checkedOutAt)))
                : '';

            fputcsv($handle, [
                $attendance->child?->full_name ?? '',
                $attendance->checkinGatepass?->code ?? '',
                $attendance->checkinGatepass?->guardian?->full_name ?? '',
                $attendance->checkinKeeper?->user?->name ?? '',
                $checkedInAt?->setTimezone($timezone)->format('Y-m-d g:i A') ?? '',
                $attendance->checkoutGatepass?->code ?? '',
                $attendance->checkoutGatepass?->guardian?->full_name ?? '',
                $attendance->checkoutKeeper?->user?->name ?? '',
                $checkedOutAt?->setTimezone($timezone)->format('Y-m-d g:i A') ?? '',
                $duration,
            ], escape: '\\');
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return $csvContent;
    }
}
