<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Subdomain;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $organization = Subdomain::organization();
        $panelId = $panel->getId();

        if ($panelId === 'admin' && $organization !== null) {
            return Keeper::where('organization_id', $organization->id)
                ->where('user_id', $this->id)
                ->exists();
        }

        if ($panelId === 'keeper' && $organization === null) {
            return $this->keeper_id !== null;
        }

        return false;
    }

    /** @return BelongsToMany<Organization, $this, Keeper> */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->using(Keeper::class);
    }

    /**
     * @return BelongsTo<Keeper, $this>
     */
    public function keeper(): BelongsTo
    {
        return $this->belongsTo(Keeper::class);
    }

    /**
     * @return HasMany<Guardian, $this>
     */
    public function guardians(): HasMany
    {
        return $this->hasMany(Guardian::class);
    }

    /**
     * @return HasManyThrough<Child, Relationship, $this>
     */
    public function children(): HasManyThrough
    {
        return $this->hasManyThrough(Child::class, Relationship::class, 'guardian_id', 'id', 'guardian_id', 'child_id');
    }
}
