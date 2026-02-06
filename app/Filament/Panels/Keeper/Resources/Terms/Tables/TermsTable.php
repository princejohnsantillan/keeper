<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Terms\Tables;

use App\Filament\Actions\ArchiveTermAction;
use App\Filament\Components\Tables\AppTextColumn;
use App\Filament\Panels\Keeper\Resources\Terms\Schemas\TermInfolist;
use App\Models\Term;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class TermsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                AppTextColumn::name(),
                self::archivedAtColumn(),
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
                    ->schema(fn (Schema $schema): Schema => TermInfolist::configure($schema))
                    ->modalFooterActions(fn (ViewAction $action): array => [
                        EditAction::make()
                            ->slideOver()
                            ->cancelParentActions()
                            ->hidden(fn (Term $record): bool => $record->isArchived()),
                        ArchiveTermAction::make()
                            ->cancelParentActions(),
                        DeleteAction::make()
                            ->cancelParentActions()
                            ->hidden(fn (Term $record): bool => $record->isArchived()),
                    ]),
            ]);
    }

    private static function archivedAtColumn(): TextColumn
    {
        return TextColumn::make('archived_at')
            ->label('Status')
            ->badge()
            ->formatStateUsing(fn (?string $state): string => $state ? 'Archived' : 'Active')
            ->color(fn (?string $state): string => $state ? 'danger' : 'success');
    }

    private static function createdAtColumn(): TextColumn
    {
        return TextColumn::make('created_at')
            ->label('Created')
            ->dateTime('M d, Y')
            ->sortable();
    }
}
