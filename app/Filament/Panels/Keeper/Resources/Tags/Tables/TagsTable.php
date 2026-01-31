<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Tags\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class TagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                self::nameColumn(),
                self::usageCountColumn(),
            ])
            ->defaultSort('name', 'asc')
            ->modifyQueryUsing(function (Builder $query): Builder {
                return $query->addSelect([
                    'usage_count' => DB::table('taggables')
                        ->selectRaw('count(*)')
                        ->whereColumn('taggables.tag_id', 'tags.id'),
                ]);
            })
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Edit Tag'),
                DeleteAction::make(),
            ]);
    }

    private static function nameColumn(): TextColumn
    {
        return TextColumn::make('name')
            ->label('Tag')
            ->searchable()
            ->sortable();
    }

    private static function usageCountColumn(): TextColumn
    {
        return TextColumn::make('usage_count')
            ->label('Used')
            ->sortable()
            ->alignEnd();
    }
}
