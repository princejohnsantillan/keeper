<?php

declare(strict_types=1);

namespace App\Filament\Components\Tables;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

final class AppTagsColumn
{
    public static function tags(string $field = 'organizationTags', ?string $label = null): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->badge()
            ->getStateUsing(function (Model $record): array {
                if (! method_exists($record, 'organizationTags')) {
                    return [];
                }

                return $record->organizationTags()->pluck('name')->toArray();
            })
            ->searchable(query: fn ($query, string $search) => $query->whereHas(
                'organizationTags',
                fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%'])
            ));
    }
}
