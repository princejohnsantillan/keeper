<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin IdeHelperActivity
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $location
 * @property string|null $location_map_link
 * @property \Carbon\CarbonImmutable $starts_at
 * @property \Carbon\CarbonImmutable $ends_at
 * @property \Carbon\CarbonImmutable|null $published_at
 * @property string|null $notes
 * @property int $organization_id
 * @property int|null $term_id
 * @property int|null $message_id
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance
 * @property-read int|null $attendance_count
 * @property-read \App\Models\Attendance|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Child> $children
 * @property-read int|null $children_count
 * @property-read \App\Models\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Gatepass> $gatepasses
 * @property-read int|null $gatepasses_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Message|null $message
 * @property-read \App\Models\Organization $organization
 * @property-read \App\Models\Term|null $term
 *
 * @method static \Database\Factories\ActivityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereLocationMapLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereTermId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Activity whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[ScopedBy(OrganizationScope::class)]
final class Activity extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ActivityFactory> */
    use HasFactory;

    use InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'starts_at' => 'immutable_datetime',
            'ends_at' => 'immutable_datetime',
            'published_at' => 'immutable_datetime',
        ];
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
            ->width(800)
            ->height(600)
            ->sharpen(10);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->singleFile();
    }
}
