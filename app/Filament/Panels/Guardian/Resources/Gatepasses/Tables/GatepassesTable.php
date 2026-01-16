<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Gatepasses\Tables;

use App\Avatar;
use App\Models\Gatepass;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class GatepassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->paginated(false)
            ->defaultSort('created_at', 'desc')
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Stack::make([
                    self::codeColumn(),
                    self::activityTitleColumn(),
                    Split::make([
                        self::childAvatarColumn(),
                        self::childFullNameColumn(),
                    ]),
                    Split::make([
                        self::guardianAvatarColumn(),
                        self::guardianFullNameColumn(),
                    ]),
                ])->space(2),
            ])
            ->recordAction('view')
            ->recordActions([]);
    }

    private static function codeColumn(): TextColumn
    {
        return TextColumn::make('code')
            ->weight(FontWeight::Bold)
            ->size(TextSize::Large);
    }

    private static function activityTitleColumn(): TextColumn
    {
        return TextColumn::make('activity.title')
            ->weight(FontWeight::Medium)
            ->color('gray');
    }

    private static function childAvatarColumn(): SpatieMediaLibraryImageColumn
    {
        return SpatieMediaLibraryImageColumn::make('child.avatar')
            ->collection('avatar')
            ->circular()
            ->defaultImageUrl(fn (Gatepass $record): string => Avatar::generateUrl($record->child->full_name))
            ->grow(false);
    }

    private static function childFullNameColumn(): TextColumn
    {
        return TextColumn::make('child.full_name')
            ->label('Child');
    }

    private static function guardianAvatarColumn(): SpatieMediaLibraryImageColumn
    {
        return SpatieMediaLibraryImageColumn::make('guardian.avatar')
            ->collection('avatar')
            ->circular()
            ->defaultImageUrl(fn (Gatepass $record): string => Avatar::generateUrl($record->guardian->full_name))
            ->grow(false);
    }

    private static function guardianFullNameColumn(): TextColumn
    {
        return TextColumn::make('guardian.full_name')
            ->label('Guardian');
    }
}
