<?php

namespace App\Models;

use App\Models\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperActivity
 */
#[ScopedBy(OrganizationScope::class)]
final class Activity extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityFactory> */
    use HasFactory;

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
     * @return BelongsToMany<Child, $this, Attendance>
     */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Child::class, 'attendance')
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
}
