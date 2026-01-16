<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Gender;
use Database\Factories\ChildFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
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
 * @mixin IdeHelperChild
 */
final class Child extends Model implements HasMedia
{
    /** @use HasFactory<ChildFactory> */
    use HasFactory;

    use HasTags;
    use HasUlids;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $keyType = 'string';

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
