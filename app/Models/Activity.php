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
