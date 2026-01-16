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
            'deprecated_at' => 'immutable_datetime',
        ];
    }

    public function isDeprecated(): bool
    {
        return $this->deprecated_at !== null;
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
