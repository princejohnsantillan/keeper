<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Keepers\Pages;

use App\Actions\GetCurrentKeeperAction;
use App\Filament\Panels\Keeper\Resources\Keepers\KeeperResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditKeeper extends EditRecord
{
    protected static string $resource = KeeperResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $currentKeeper = app(GetCurrentKeeperAction::class)->__invoke();

        if (! $currentKeeper?->isAdmin()) {
            abort(403);
        }
    }

    /**
     * @return array<\Filament\Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
