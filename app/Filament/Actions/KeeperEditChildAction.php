<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\UpdateChildAction;
use App\Facades\Subdomain;
use App\Models\Child;
use App\Models\Ownership;
use Filament\Actions\EditAction;

final class KeeperEditChildAction
{
    public static function make(?string $name = 'edit'): EditAction
    {
        return EditAction::make($name)
            ->slideOver()
            ->visible(function (Child $record): bool {
                $organization = Subdomain::organization();

                if ($organization === null) {
                    return false;
                }

                return $record->ownerships->contains(
                    fn (Ownership $ownership): bool => $ownership->owner_type === $organization->getMorphClass()
                        && $ownership->owner_id === (string) $organization->getKey(),
                );
            })
            ->using(function (Child $record, array $data, UpdateChildAction $updateChild): Child {
                return $updateChild($record, $data);
            });
    }
}
