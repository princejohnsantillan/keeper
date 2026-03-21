<?php

declare(strict_types=1);

namespace App\Models;

use App\Facades\Subdomain;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperUser
 */
final class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasUlids, Notifiable;

    protected $keyType = 'string';

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

    /**
     * @return Attribute<string, string>
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): string => strtolower($value),
        );
    }

    public function needsPasswordSetup(): bool
    {
        return $this->password === null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->needsPasswordSetup()) {
            return false;
        }

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
     * @return HasOne<Keeper, $this>
     */
    public function keeper(): HasOne
    {
        return $this->hasOne(Keeper::class);
    }

    /**
     * @return HasMany<Organization, $this>
     */
    public function ownedOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'owner_id');
    }

    /**
     * @return BelongsTo<Guardian, $this>
     */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    /**
     * @return HasMany<Activity, $this>
     */
    public function createdActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'created_by');
    }

    /**
     * @return HasMany<AttendanceStickerPrintSetting, $this>
     */
    public function attendanceStickerPrintSettings(): HasMany
    {
        return $this->hasMany(AttendanceStickerPrintSetting::class);
    }
}
