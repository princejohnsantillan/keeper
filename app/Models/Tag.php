<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\OrganizationScope;
use App\Subdomain;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(OrganizationScope::class)]
final class Tag extends Model
{
    protected $fillable = [
        'name',
        'organization_id',
    ];

    /**
     * @return Attribute<string, string>
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): string => strtolower($value),
        );
    }

    protected static function booted(): void
    {
        self::creating(function (Tag $tag): void {
            if ($tag->organization_id === null) {
                $tag->organization_id = Subdomain::organization()?->id;
            }
        });
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public static function findFromString(string $name): ?static
    {
        return self::query()
            ->where('organization_id', Subdomain::organization()?->id)
            ->where('name', strtolower($name))
            ->first();
    }

    public static function findOrCreateFromString(string $name): static
    {
        $tag = static::findFromString($name);

        if (! $tag) {
            $tag = static::create([
                'name' => $name,
            ]);
        }

        return $tag;
    }
}
