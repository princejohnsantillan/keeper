<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\OrganizationTag;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasOrganizationTags
{
    /**
     * @return MorphMany<OrganizationTag, $this>
     */
    public function organizationTags(): MorphMany
    {
        return $this->morphMany(OrganizationTag::class, 'taggable');
    }
}
