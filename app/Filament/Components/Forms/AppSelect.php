<?php

declare(strict_types=1);

namespace App\Filament\Components\Forms;

use App\Enums\Gender;
use App\Enums\Relationship;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

final class AppSelect
{
    public static function relationship(string $field = 'relationship', string $label = 'Relationship'): Select
    {
        return Select::make($field)->label($label)
            ->options(Relationship::class)
            ->required();
    }

    public static function term(string $field = 'term_id', string $label = 'Terms & Conditions'): Select
    {
        return Select::make($field)->label($label)
            ->relationship(
                name: 'term',
                titleAttribute: 'name',
                modifyQueryUsing: fn (Builder $query): Builder => $query->whereNull('deprecated_at'),
            )
            ->native(false);
    }

    public static function gender(string $field = 'gender', string $label = 'Gender'): Select
    {
        return Select::make($field)->label($label)
            ->options(Gender::class)
            ->required();
    }
}
