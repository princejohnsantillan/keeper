<?php

namespace App\Models\Scopes;

use App\Subdomain;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrganizationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $organization = Subdomain::organization();

        if ($organization !== null) {
            $builder->where('organization_id', $organization->id);
        }
    }
}
