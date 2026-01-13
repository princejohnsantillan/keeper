<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Filament\Panels\Guardian\Resources\Activities\ActivityResource;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

final class BackToActivitiesAction
{
    public static function make(?string $name = 'back_to_activities', string $label = 'Back to Activities'): Action
    {
        return Action::make($name)->label($label)
            ->icon(Heroicon::ArrowLeft)
            ->color('gray')
            ->url(ActivityResource::getUrl('index'));
    }
}
