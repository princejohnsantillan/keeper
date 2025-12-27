<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\AuthUser;
use App\Models\Child;
use App\Models\Relationship;
use Filament\Actions\CreateAction;

final class CreateChildAction
{
    public static function make(?string $name = null, string $label = 'Add child'): CreateAction
    {
        return CreateAction::make($name)->label($label)
            ->modalHeading('Add Child')
            ->modalSubmitActionLabel('Add')
            ->createAnother(false)
            ->using(function (array $data): Child {
                $relationship = $data['relationship'];
                unset($data['relationship']);

                $guardian = AuthUser::guardian();
                $child = Child::query()->create($data);

                Relationship::create([
                    'guardian_id' => $guardian->id,
                    'child_id' => $child->id,
                    'relationship' => $relationship,
                    'is_primary' => true,
                ]);

                return $child;
            });
    }
}
