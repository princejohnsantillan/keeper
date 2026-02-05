<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Facades\Subdomain;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class OrganizationScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $organization = Subdomain::organization();

        if ($organization !== null) {
            $builder->where('organization_id', $organization->id);
        }
    }
}
