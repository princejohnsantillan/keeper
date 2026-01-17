<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Guardians\Tables;

use App\Filament\Actions\DeleteGuardianAction;
use App\Filament\Actions\EditGuardianAction;
use App\Filament\Components\Tables\AppSpatieMediaLibraryImageColumn;
use App\Filament\Panels\Guardian\Resources\Guardians\GuardianResource;
use App\Models\Guardian;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class GuardiansTable
{
    private static bool $isSortable = false;

    public static function configure(Table $table): Table
    {
        self::$isSortable = GuardianResource::getEloquentQuery()->count() >= 3;

        return $table
            ->searchable(false)
            ->paginated(false)
            ->persistSortInSession()
            ->defaultSort('first_name', 'asc')
            ->contentGrid([
                'sm' => 1,
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Stack::make([
                    Split::make([
                        AppSpatieMediaLibraryImageColumn::avatar()
                            ->imageSize(80)
                            ->grow(false),

                        Stack::make([
                            self::fullNameColumn(),
                            self::birthDateColumn(),
                        ]),
                    ]),

                    Stack::make([
                        self::emailColumn(),
                        self::phoneColumn(),
                    ]),
                ])->space(3),
            ]);
    }

    private static function fullNameColumn(): TextColumn
    {
        return TextColumn::make('full_name')
            ->label('Name')
            ->weight(FontWeight::Bold)
            ->icon(fn (Guardian $record) => $record->gender->getIcon())
            ->iconColor(fn (Guardian $record) => $record->gender->getColor())
            ->sortable(self::$isSortable ? ['first_name', 'last_name'] : false);
    }

    private static function birthDateColumn(): TextColumn
    {
        return TextColumn::make('birth_date')
            ->date('d M Y')
            ->suffix(fn (Guardian $record): string => ' · '.$record->birth_date->age.' yrs')
            ->sortable(self::$isSortable)
            ->color('gray');
    }

    private static function emailColumn(): TextColumn
    {
        return TextColumn::make('email')
            ->icon(Heroicon::Envelope)
            ->sortable(self::$isSortable)
            ->copyable();
    }

    private static function phoneColumn(): TextColumn
    {
        return TextColumn::make('phone')
            ->icon(Heroicon::Phone)
            ->copyable();
    }

    public static function getEditAction(): EditAction
    {
        return EditGuardianAction::make();
    }

    public static function getDeleteAction(): DeleteAction
    {
        return DeleteGuardianAction::make();
    }
}
