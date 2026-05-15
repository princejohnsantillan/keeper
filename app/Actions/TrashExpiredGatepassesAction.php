<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Gatepass;
use App\Models\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Builder;

final class TrashExpiredGatepassesAction
{
    public function __invoke(): int
    {
        $cutoff = now()->subDay();

        return Gatepass::query()
            ->whereHas(
                'activity',
                fn (Builder $query): Builder => $query
                    ->withoutGlobalScope(OrganizationScope::class)
                    ->where('ends_at', '<', $cutoff)
            )
            ->delete();
    }
}
