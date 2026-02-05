<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Gatepasses\Schemas;

use App\Avatar;
use App\Models\Gatepass;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

final class GatepassInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->compact()
                    ->extraAttributes(['class' => 'max-w-md mx-auto'])
                    ->schema([
                        self::qrCodeEntry(),
                        self::activitySection(),
                        self::childSection(),
                        self::guardianSection(),
                    ]),
            ]);
    }

    private static function qrCodeEntry(): ViewEntry
    {
        return ViewEntry::make('code')
            ->hiddenLabel()
            ->view('filament.infolists.components.qr-code-entry');
    }

    private static function activitySection(): Grid
    {
        return Grid::make(1)
            ->schema([
                TextEntry::make('activity.title')
                    ->hiddenLabel()
                    ->weight(FontWeight::SemiBold)
                    ->size(TextSize::Large)
                    ->icon(Heroicon::Play)
                    ->iconColor('primary'),
                TextEntry::make('activity.starts_at')
                    ->hiddenLabel()
                    ->dateTime('M d, Y \a\t h:i A')
                    ->icon(Heroicon::Calendar)
                    ->color('gray'),
                TextEntry::make('activity.location')
                    ->hiddenLabel()
                    ->icon(Heroicon::MapPin)
                    ->color('gray'),
            ]);
    }

    private static function childSection(): Flex
    {
        return Flex::make([
            SpatieMediaLibraryImageEntry::make('child.avatar')
                ->hiddenLabel()
                ->collection('avatar')
                ->circular()
                ->size(48)
                ->grow(false)
                ->defaultImageUrl(fn (Gatepass $record): string => Avatar::generateUrl($record->child->full_name)),
            TextEntry::make('child.full_name')
                ->hiddenLabel(),
        ])->verticallyAlignCenter();
    }

    private static function guardianSection(): Flex
    {
        return Flex::make([
            SpatieMediaLibraryImageEntry::make('guardian.avatar')
                ->hiddenLabel()
                ->collection('avatar')
                ->circular()
                ->size(48)
                ->grow(false)
                ->defaultImageUrl(fn (Gatepass $record): string => Avatar::generateUrl($record->guardian->full_name)),
            TextEntry::make('guardian.full_name')
                ->hiddenLabel(),
            TextEntry::make('relationship')
                ->hiddenLabel()
                ->badge()
                ->getStateUsing(function (Gatepass $record): ?string {
                    $relationship = \App\Models\Relationship::query()
                        ->where('guardian_id', $record->guardian_id)
                        ->where('child_id', $record->child_id)
                        ->first();

                    return $relationship?->relationship?->getLabel();
                }),
        ])->verticallyAlignCenter();
    }
}
