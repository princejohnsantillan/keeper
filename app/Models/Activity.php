<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\OrganizationScope;
use Database\Factories\ActivityFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[ScopedBy(OrganizationScope::class)]
/**
 * @mixin IdeHelperActivity
 */
final class Activity extends Model implements HasMedia
{
    /** @use HasFactory<ActivityFactory> */
    use HasFactory;

    use HasUlids;
    use InteractsWithMedia;

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
            'starts_at' => 'immutable_datetime',
            'ends_at' => 'immutable_datetime',
            'publish_at' => 'immutable_datetime',
            'checkin_opened_at' => 'immutable_datetime',
            'checkin_closed_at' => 'immutable_datetime',
            'summary_report_sent_at' => 'immutable_datetime',
            'promotion_broadcast_sent_at' => 'immutable_datetime',
        ];
    }

    public function isPrivate(): bool
    {
        return $this->is_private;
    }

    public function hasCheckInOpened(): bool
    {
        return $this->checkin_opened_at !== null;
    }

    public function hasCheckInClosed(): bool
    {
        return $this->checkin_closed_at !== null;
    }

    public function isCheckInOpen(): bool
    {
        return $this->hasCheckInOpened() && (! $this->hasCheckInClosed());
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo<Term, $this>
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * @return BelongsToMany<Child, $this, Attendance>
     */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Child::class, 'attendances')
            ->using(Attendance::class)
            ->withTimestamps();
    }

    /**
     * @return HasMany<Gatepass, $this>
     */
    public function gatepasses(): HasMany
    {
        return $this->hasMany(Gatepass::class);
    }

    /**
     * @return HasMany<Attendance, $this>
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * @return HasMany<Invitation, $this>
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * @return BelongsTo<Message, $this>
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->nonQueued()
            ->fit(Fit::Crop, 960, 540)
            ->sharpen(10);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->useDisk('public')
            ->singleFile();
    }
}
