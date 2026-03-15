<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Activities\Pages;

use App\Avatar;
use App\Filament\Panels\Keeper\Resources\Activities\ActivityResource;
use App\Models\Activity;
use App\Models\Attendance;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                self::childAvatarColumn(),
                self::childFullNameColumn(),
                self::checkinColumn(),
                self::checkedInAtColumn(),
                self::checkoutColumn(),
                self::checkedOutAtColumn(),
                TextColumn::make('notes')
                    ->wrap(),
            ])
            ->filters([])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }

    private static function childAvatarColumn(): SpatieMediaLibraryImageColumn
    {
        return SpatieMediaLibraryImageColumn::make('child.avatar')
            ->label('')
            ->collection('avatar')
            ->circular()
            ->defaultImageUrl(fn (Attendance $record): string => Avatar::generateUrl($record->child->full_name));
    }

    private static function childFullNameColumn(): TextColumn
    {
        return TextColumn::make('child.full_name')
            ->label('Child')
            ->icon(fn (Attendance $record): string => $record->child->gender->getIcon())
            ->iconColor(fn (Attendance $record): array => $record->child->gender->getColor())
            ->searchable(query: function (Builder $query, string $search): Builder {
                return $query->whereHas('child', function (Builder $query) use ($search): void {
                    $query->where('first_name', 'ilike', "%{$search}%")
                        ->orWhere('last_name', 'ilike', "%{$search}%");
                });
            })
            ->sortable(query: function (Builder $query, string $direction): Builder {
                return $query
                    ->join('children', 'attendances.child_id', '=', 'children.id')
                    ->orderBy('children.first_name', $direction)
                    ->orderBy('children.last_name', $direction)
                    ->select('attendances.*');
            });
    }

    private static function checkinColumn(): TextColumn
    {
        return TextColumn::make('checkinGatepass.code')
            ->label('Check-in')
            ->description(fn (Attendance $record): string => $record->checkinGatepass?->guardian?->full_name ?? '—');
    }

    private static function checkoutColumn(): TextColumn
    {
        return TextColumn::make('checkoutGatepass.code')
            ->label('Check-out')
            ->description(fn (Attendance $record): string => $record->checkoutGatepass?->guardian?->full_name ?? '—');
    }

    private static function checkedInAtColumn(): TextColumn
    {
        return TextColumn::make('checked_in_at')
            ->label('Checked In')
            ->dateTime('h:i A')
            ->description(fn (Attendance $record): ?string => $record->checkinKeeper?->user?->name)
            ->sortable();
    }

    private static function checkedOutAtColumn(): TextColumn
    {
        return TextColumn::make('checked_out_at')
            ->label('Checked Out')
            ->dateTime('h:i A')
            ->description(fn (Attendance $record): ?string => $record->checkoutKeeper?->user?->name)
            ->sortable();
    }
}
