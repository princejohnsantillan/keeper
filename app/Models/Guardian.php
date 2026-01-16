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
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;

/**
 * @mixin IdeHelperGuardian
 *
 * @property int $id
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property \Carbon\CarbonImmutable $birth_date
 * @property Gender $gender
 * @property string $email
 * @property string|null $phone
 * @property int|null $user_id
 * @property int $owner_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Term> $acceptedTerms
 * @property-read int|null $accepted_terms_count
 * @property-read \App\Models\Relationship|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Child> $children
 * @property-read int|null $children_count
 * @property-read string $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Gatepass> $gatepasses
 * @property-read int|null $gatepasses_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Organization> $organizations
 * @property-read int|null $organizations_count
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Relationship> $relationships
 * @property-read int|null $relationships_count
 * @property \Illuminate\Database\Eloquent\Collection<int, \Spatie\Tags\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TermAcceptance> $termAcceptances
 * @property-read int|null $term_acceptances_count
 * @property-read \App\Models\User|null $user
 *
 * @method static \Database\Factories\GuardianFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian withAllTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian withAllTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian withAnyTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian withAnyTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian withAnyTagsOfType(array|string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian withoutTags(\ArrayAccess|\Spatie\Tags\Tag|array|string $tags, ?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guardian withoutTrashed()
 *
 * @mixin \Eloquent
 */
final class Guardian extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\GuardianFactory> */
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
