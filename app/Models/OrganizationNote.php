<?php

declare(strict_types=1);

namespace App\Models;

use App\Facades\Subdomain;
use App\Models\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

#[ScopedBy(OrganizationScope::class)]
/**
 * @mixin IdeHelperOrganizationNote
 */
final class OrganizationNote extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationNoteFactory> */
    use HasFactory;

    use HasUlids;

    protected $keyType = 'string';

    protected $fillable = [
        'organization_id',
        'note',
    ];

    protected static function booted(): void
    {
        self::creating(function (OrganizationNote $organizationNote): void {
            if (isset($organizationNote->organization_id)) {
                return;
            }

            $organizationId = Subdomain::organization()?->id;

            if ($organizationId === null) {
                return;
            }

            $organizationNote->organization_id = $organizationId;
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
    public function notable(): MorphTo
    {
        return $this->morphTo();
    }
}
