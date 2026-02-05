<?php

declare(strict_types=1);

namespace App\Filament\Components\Tables;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

final class AppTagsColumn
{
    public static function tags(string $field = 'tags', ?string $label = null): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->badge()
            ->getStateUsing(function (Model $record): array {
                if (! method_exists($record, 'tags')) {
                    return [];
                }

                return $record->tags()->pluck('name')->toArray();
            })
            ->searchable(query: fn ($query, string $search) => $query->whereHas(
                'tags',
                fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%'])
            ));
    }
}
