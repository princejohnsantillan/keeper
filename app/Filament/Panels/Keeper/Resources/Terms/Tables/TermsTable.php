<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Terms\Tables;

use App\Filament\Components\Tables\AppTextColumn;
use App\Filament\Panels\Keeper\Resources\Terms\Schemas\TermInfolist;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class TermsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                AppTextColumn::name(),
                self::createdAtColumn(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->recordAction('view')
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->icon('')
                    ->extraAttributes(['class' => 'hidden'])
                    ->slideOver(false)
                    ->modalHeading('Terms & Conditions')
                    ->infolist(fn ($infolist) => TermInfolist::configure($infolist))
                    ->modalFooterActions(fn (ViewAction $action): array => [
                        EditAction::make()
                            ->slideOver()
                            ->cancelParentActions(),
                        DeleteAction::make()
                            ->cancelParentActions(),
                    ]),
            ]);
    }

    private static function createdAtColumn(): TextColumn
    {
        return TextColumn::make('created_at')
            ->label('Created')
            ->dateTime('M d, Y')
            ->sortable();
    }
}
