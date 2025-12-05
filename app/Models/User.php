<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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
        return true;
        //        $organization = Context::getHidden('organization');
        //
        //        if (! $organization instanceof Organization) { // Is Root Domain
        //            return false;
        //        }
        //
        //        if ($panel->getId() === 'dashboard') {
        //            return true;
        //        }
        //
        //        if ($panel->getId() === 'admin' && OrganizationUser::query()->where('user_id', $this->id)->where('organization_user.organization_id', $organization->id)->exists()) {
        //            return true;
        //        }
        //
        //        return false;
    }

    /** @return BelongsToMany<Organization, $this, OrganizationUser> */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->using(OrganizationUser::class);
    }

    /**
     * @return HasOne<Keeper, $this>
     */
    public function keeper(): HasOne
    {
        return $this->hasOne(Keeper::class);
    }

    /**
     * @return HasManyThrough<Child, Relationship, $this>
     */
    public function children(): HasManyThrough
    {
        return $this->hasManyThrough(Child::class, Relationship::class, 'keeper_id', 'id', 'keeper_id', 'child_id');
    }
}
