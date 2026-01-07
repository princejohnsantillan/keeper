<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @mixin IdeHelperGuardian
 */
final class Guardian extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\GuardianFactory> */
    use HasFactory;

    use InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'gender' => Gender::class,
            'birth_date' => 'immutable_datetime',
        ];
    }

    /** @return Attribute<string,never> */
    public function fullName(): Attribute
    {
        return Attribute::make(fn (): string => $this->first_name.' '.$this->last_name);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany<Child, $this, Relationship>
     */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Child::class, 'relationships')
            ->using(Relationship::class)
            ->withPivot('relationship')
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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile();
    }
}
