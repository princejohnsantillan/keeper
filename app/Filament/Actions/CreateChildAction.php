<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\CreateChildAction as CreateChildBusinessAction;
use App\AuthUser;
use App\Enums\Relationship;
use App\Models\Child;
use Filament\Actions\CreateAction;

final class CreateChildAction
{
    public static function make(?string $name = null, string $label = 'Add child'): CreateAction
    {
        return CreateAction::make($name)->label($label)
            ->modalHeading('Add Child')
            ->modalSubmitActionLabel('Add')
            ->createAnother(false)
            ->using(function (array $data, CreateChildBusinessAction $createChildAction): Child {
                /** @var Relationship $relationship */
                $relationship = $data['relationship'];

                unset($data['relationship']);

                $guardian = AuthUser::guardian();

                return $createChildAction($data, $guardian, $relationship);
            });
    }
}
