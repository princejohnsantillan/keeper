<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\UpdateGuardianAction;
use App\Facades\Subdomain;
use App\Models\Guardian;
use App\Models\Ownership;
use Filament\Actions\EditAction;

final class KeeperEditGuardianAction
{
    public static function make(?string $name = 'edit'): EditAction
    {
        return EditAction::make($name)
            ->slideOver()
            ->visible(function (Guardian $record): bool {
                $organization = Subdomain::organization();

                if ($organization === null) {
                    return false;
                }

                return $record->ownerships->contains(
                    fn (Ownership $ownership): bool => $ownership->owner_type === $organization->getMorphClass()
                        && $ownership->owner_id === (string) $organization->getKey(),
                );
            })
            ->using(function (Guardian $record, array $data, UpdateGuardianAction $updateGuardian): Guardian {
                return $updateGuardian($record, $data);
            });
    }
}
