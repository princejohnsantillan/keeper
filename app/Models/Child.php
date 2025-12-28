<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Gender;
use Database\Factories\ChildFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperChild
 */
final class Child extends Model
{
    /** @use HasFactory<ChildFactory> */
    use HasFactory;

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

    /** @return Attribute<string,never> */
    public function fullName(): Attribute
    {
        return Attribute::make(fn (): string => Str::squish("{$this->first_name} {$this->middle_name} {$this->last_name}"));
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
        return $this->belongsToMany(Activity::class, 'attendance')
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
}
