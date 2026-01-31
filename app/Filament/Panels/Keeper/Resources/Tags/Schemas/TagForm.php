<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Tags\Schemas;

use App\Filament\Components\Forms\AppTextInput;
use App\Models\Tag;
use Closure;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

final class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                AppTextInput::name()
                    ->label('Tag name')
                    ->required()
                    ->autofocus()
                    ->rules([
                        fn (?Model $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                            $exists = Tag::query()
                                ->whereRaw('LOWER(name) = LOWER(?)', [$value])
                                ->when($record, fn ($query) => $query->whereNot('id', $record->id))
                                ->exists();

                            if ($exists) {
                                $fail('A tag with this name already exists.');
                            }
                        },
                    ]),
            ]);
    }
}
