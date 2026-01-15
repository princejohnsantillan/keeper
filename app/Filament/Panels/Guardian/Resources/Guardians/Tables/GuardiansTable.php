<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Guardians\Tables;

use App\Enums\Relationship as RelationshipEnum;
use App\Filament\Actions\DeleteGuardianAction;
use App\Filament\Actions\EditGuardianAction;
use App\Filament\Components\Tables\AppSpatieMediaLibraryImageColumn;
use App\Models\Guardian;
use App\Models\Relationship;
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
    public static function configure(Table $table): Table
    {
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

                    self::childrenColumn(),
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
            ->sortable(['first_name', 'last_name']);
    }

    private static function birthDateColumn(): TextColumn
    {
        return TextColumn::make('birth_date')
            ->date('d M Y')
            ->suffix(fn (Guardian $record): string => ' · '.$record->birth_date->age.' yrs')
            ->sortable()
            ->color('gray');
    }

    private static function emailColumn(): TextColumn
    {
        return TextColumn::make('email')
            ->icon(Heroicon::Envelope)
            ->sortable()
            ->copyable();
    }

    private static function phoneColumn(): TextColumn
    {
        return TextColumn::make('phone')
            ->icon(Heroicon::Phone)
            ->copyable();
    }

    private static function childrenColumn(): TextColumn
    {
        return TextColumn::make('children.id')
            ->label('Children')
            ->icon(function (string $state, Guardian $record): ?string {
                $child = $record->children->firstWhere('id', $state);

                return $child?->gender->getIcon();
            })
            ->iconColor(function (string $state, Guardian $record): ?array {
                $child = $record->children->firstWhere('id', $state);

                return $child?->gender->getColor();
            })
            ->formatStateUsing(function (string $state, Guardian $record): string {
                $child = $record->children->firstWhere('id', $state);

                if (! $child) {
                    return '';
                }

                /** @var Relationship $pivot */
                $pivot = $child->pivot;
                /** @var RelationshipEnum|null $relationship */
                $relationship = $pivot->relationship;

                $inverseLabel = $relationship?->inverse($child->gender)->getLabel() ?? '';
                $age = $child->birth_date->age;

                return $child->getNickname().", {$age} yrs".($inverseLabel ? " ({$inverseLabel})" : '');
            })
            ->listWithLineBreaks();
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
