<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Activities\Pages;

use App\Filament\Actions\CheckInAttendanceAction;
use App\Filament\Actions\CheckOutAttendanceAction;
use App\Filament\Panels\Keeper\Resources\Activities\ActivityResource;
use App\Models\Activity;
use App\Models\Attendance;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ViewAttendance extends ManageRelatedRecords
{
    protected static string $resource = ActivityResource::class;

    protected static string $relationship = 'attendance';

    protected static ?string $title = 'Attendance';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        /** @var Activity $activity */
        $activity = $this->getOwnerRecord();

        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('child.full_name')
                    ->label('Child')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                TextColumn::make('checkinGatepass.code')
                    ->label('Check-in Code'),
                TextColumn::make('checked_in_at')
                    ->label('Checked In')
                    ->dateTime('h:i A')
                    ->description(fn (Attendance $record): ?string => $record->checked_in_at?->format('M d, Y'))
                    ->sortable(),
                TextColumn::make('checkinKeeper.user.name')
                    ->label('Checked In By'),
                TextColumn::make('checkoutGatepass.code')
                    ->label('Check-out Code'),
                TextColumn::make('checked_out_at')
                    ->label('Checked Out')
                    ->dateTime('h:i A')
                    ->description(fn (Attendance $record): ?string => $record->checked_out_at?->format('M d, Y'))
                    ->sortable(),
                TextColumn::make('checkoutKeeper.user.name')
                    ->label('Checked Out By'),
                TextColumn::make('notes')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->headerActions([
                CheckInAttendanceAction::make($activity),
                CheckOutAttendanceAction::make($activity),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
