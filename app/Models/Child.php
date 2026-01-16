<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Gender;
use Database\Factories\ChildFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;

/**
 * @property int $id
 * @property int $owner_id
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string|null $nickname
 * @property \Carbon\CarbonImmutable $birth_date
 * @property Gender $gender
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Relationship|\App\Models\Attendance|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance
 * @property-read int|null $attendance_count
 * @property-read string $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Gatepass> $gatepasses
 * @property-read int|null $gatepasses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Guardian> $guardians
 * @property-read int|null $guardians_count
 * @property-read string $known_as
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Relationship> $relationships
 * @property-read int|null $relationships_count
 * @property \Illuminate\Database\Eloquent\Collection<int, \Spatie\Tags\Tag> $tags
 * @property-read int|null $tags_count
 *
 * @method static \Database\Factories\ChildFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child withAllTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child withAllTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child withAnyTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child withAnyTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child withAnyTagsOfType(array|string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child withoutTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Child withoutTrashed()
 *
 * @mixin \Eloquent
 * @mixin IdeHelperChild
 */
final class Child extends Model implements HasMedia
{
    /** @use HasFactory<ChildFactory> */
    use HasFactory;

    use HasTags;
    use InteractsWithMedia;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'gender' => Gender::class,
            'birth_date' => 'immutable_datetime',
        ];
    }

    public function getNickname(): string
    {
        return $this->nickname ?? Str::before($this->first_name, ' ');
    }

    /**
     * @return Attribute<string,never>
     */
    public function knownAs(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->nickname ?? Str::before($this->first_name, ' '),
        );
    }

    /** @return Attribute<string,never> */
    public function fullName(): Attribute
    {
        return Attribute::make(fn (): string => Str::squish("{$this->first_name} {$this->middle_name} {$this->last_name}"));
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return BelongsToMany<Guardian, $this, Relationship>
     */
    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class, 'relationships')
            ->using(Relationship::class)
            ->withPivot('relationship');
    }

    /**
     * @return BelongsToMany<Activity, $this, Attendance>
     */
    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'attendances')
            ->using(Attendance::class)
            ->withTimestamps();
    }

    /**
     * @return HasMany<Relationship, $this>
     */
    public function relationships(): HasMany
    {
        return $this->hasMany(Relationship::class);
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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile();
    }
}
