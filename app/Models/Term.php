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

/**
 * @property int $id
 * @property string $name
 * @property string $content
 * @property int $version
 * @property \Carbon\CarbonImmutable|null $published_at
 * @property \Carbon\CarbonImmutable|null $archived_at
 * @property int $organization_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TermAcceptance> $acceptances
 * @property-read int|null $acceptances_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Guardian> $acceptedByGuardians
 * @property-read int|null $accepted_by_guardians_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Organization $organization
 *
 * @method static \Database\Factories\TermFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereArchivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Term whereVersion($value)
 *
 * @mixin \Eloquent
 * @mixin IdeHelperTerm
 */
#[ScopedBy(OrganizationScope::class)]
final class Term extends Model
{
    /** @use HasFactory<\Database\Factories\TermFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'published_at' => 'immutable_datetime',
            'archived_at' => 'immutable_datetime',
        ];
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return HasMany<Activity, $this>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * @return HasMany<TermAcceptance, $this>
     */
    public function acceptances(): HasMany
    {
        return $this->hasMany(TermAcceptance::class);
    }

    /**
     * @return BelongsToMany<Guardian, $this>
     */
    public function acceptedByGuardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class, 'term_acceptances')
            ->withPivot(['ip_address', 'user_agent', 'accepted_at'])
            ->withTimestamps();
    }
}
