<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;

/**
 * @mixin IdeHelperGuardian
 */
final class Guardian extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\GuardianFactory> */
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

    /** @return Attribute<string,never> */
    public function fullName(): Attribute
    {
        return Attribute::make(fn (): string => $this->first_name.' '.$this->last_name);
    }

    /**
     * @return HasOne<User, $this>
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return BelongsToMany<Child, $this, Relationship>
     */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Child::class, 'relationships')
            ->withoutTrashed()
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

    /**
     * @return BelongsToMany<Organization, $this>
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->withTimestamps();
    }

    /**
     * @return HasMany<TermAcceptance, $this>
     */
    public function termAcceptances(): HasMany
    {
        return $this->hasMany(TermAcceptance::class);
    }

    /**
     * @return BelongsToMany<Term, $this>
     */
    public function acceptedTerms(): BelongsToMany
    {
        return $this->belongsToMany(Term::class, 'term_acceptances')
            ->withPivot(['ip_address', 'user_agent', 'accepted_at'])
            ->withTimestamps();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile();
    }
}
