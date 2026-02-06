<?php

declare(strict_types=1);

namespace App\Models;

use App\Facades\Subdomain;
use App\Models\Scopes\OrganizationScope;
use Database\Factories\OrganizationTagFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[ScopedBy(OrganizationScope::class)]
/**
 * @mixin IdeHelperOrganizationTag
 */
final class OrganizationTag extends Model
{
    /** @use HasFactory<OrganizationTagFactory> */
    use HasFactory;
    use HasUlids;

    protected $keyType = 'string';

    protected $fillable = [
        'organization_id',
        'name',
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
        self::creating(function (OrganizationTag $organizationTag): void {
            if (isset($organizationTag->organization_id)) {
                return;
            }

            $organizationId = Subdomain::organization()?->id;

            if ($organizationId === null) {
                return;
            }

            $organizationTag->organization_id = $organizationId;
        });
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function taggable(): MorphTo
    {
        return $this->morphTo();
    }
}
