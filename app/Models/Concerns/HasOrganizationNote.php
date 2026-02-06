<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\OrganizationNote;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasOrganizationNote
{
    /**
     * @return MorphOne<OrganizationNote, $this>
     */
    public function organizationNote(): MorphOne
    {
        return $this->morphOne(OrganizationNote::class, 'notable');
    }
}
