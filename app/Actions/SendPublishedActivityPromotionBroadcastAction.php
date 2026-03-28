<?php

declare(strict_types=1);

namespace App\Actions;

use App\Mail\ActivityPublishedPromotionBroadcastMail;
use App\Models\Activity;
use App\Models\Guardian;
use App\Models\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

final class SendPublishedActivityPromotionBroadcastAction
{
    public function __invoke(): int
    {
        $queuedBroadcasts = 0;

        Activity::query()
            ->withoutGlobalScope(OrganizationScope::class)
            ->with('organization')
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', now())
            ->whereNull('promotion_broadcast_sent_at')
            ->chunkById(100, function (EloquentCollection $activities) use (&$queuedBroadcasts): void {
                foreach ($activities as $activity) {
                    if (! $activity instanceof Activity) {
                        continue;
                    }

                    $recipientEmails = $this->resolveRecipientEmails($activity);

                    if ($recipientEmails === []) {
                        $activity->forceFill([
                            'promotion_broadcast_sent_at' => now(),
                        ])->save();

                        continue;
                    }

                    Mail::to((string) config('mail.from.address'))
                        ->bcc($recipientEmails)
                        ->queue(new ActivityPublishedPromotionBroadcastMail($activity));

                    $activity->forceFill([
                        'promotion_broadcast_sent_at' => now(),
                    ])->save();

                    $queuedBroadcasts++;
                }
            });

        return $queuedBroadcasts;
    }

    /**
     * @return list<string>
     */
    private function resolveRecipientEmails(Activity $activity): array
    {
        if ($activity->publish_at === null) {
            return [];
        }

        /** @var list<string> $recipientEmails */
        $recipientEmails = Guardian::query()
            ->with('user')
            ->whereHas('relationships', function (Builder $query) use ($activity): void {
                $query
                    ->where('is_primary', true)
                    ->whereHas('child.attendance', function (Builder $attendanceQuery) use ($activity): void {
                        $attendanceQuery
                            ->whereNotNull('checked_in_at')
                            ->whereHas('activity', function (Builder $activityQuery) use ($activity): void {
                                $activityQuery
                                    ->withoutGlobalScope(OrganizationScope::class)
                                    ->where('organization_id', $activity->organization_id)
                                    ->where('id', '!=', $activity->id)
                                    ->where('ends_at', '<', $activity->publish_at);
                            });
                    });
            })
            ->get()
            ->map(function (Guardian $guardian): ?string {
                $email = $guardian->user?->email ?? $guardian->email;

                if ($email === null) {
                    return null;
                }

                $normalizedEmail = Str::lower(trim($email));

                return $normalizedEmail !== '' ? $normalizedEmail : null;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $recipientEmails;
    }
}
