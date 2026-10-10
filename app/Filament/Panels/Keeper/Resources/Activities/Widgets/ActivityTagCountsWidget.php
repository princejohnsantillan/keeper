<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Activities\Widgets;

use App\Actions\GetActivityTagCountsAction;
use App\Actions\GetCurrentKeeperAction;
use App\Models\Activity;
use Filament\Widgets\Widget;

final class ActivityTagCountsWidget extends Widget
{
    public ?Activity $record = null;

    protected string $view = 'filament.panels.keeper.widgets.activity-tag-counts';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return app(GetCurrentKeeperAction::class)->__invoke()->isAdmin();
    }

    /**
     * @return array{tagCounts: array<string, array{registered: int, attended: int}>}
     */
    protected function getViewData(): array
    {
        return [
            'tagCounts' => $this->record === null
                ? []
                : app(GetActivityTagCountsAction::class)->__invoke($this->record),
        ];
    }
}
