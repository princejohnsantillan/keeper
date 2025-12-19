<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Subdomain;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

        if ($panelId === 'keeper' && $organization !== null) {
            return Keeper::where('organization_id', $organization->id)
                ->where('user_id', $this->id)
                ->exists();
        }

        if ($panelId === 'guardian' && $organization === null) {
            return $this->guardian()->exists();
        }

        return false;
    }

    /**
     * @return HasMany<Keeper, $this>
     */
    public function keepers(): HasMany
    {
        return $this->hasMany(Keeper::class);
    }

    /**
     * @return HasMany<Organization, $this>
     */
    public function ownedOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'owner_id');
    }

    /**
     * @return HasOne<Guardian, $this>
     */
    public function guardian(): HasOne
    {
        return $this->hasOne(Guardian::class);
    }

    /**
     * @return HasMany<Activity, $this>
     */
    public function createdActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'created_by');
    }
}
