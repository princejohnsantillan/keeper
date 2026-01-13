<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Terms\Schemas;

use App\Filament\Components\Infolists\AppTextEntry;
use Filament\Schemas\Schema;

final class TermInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                AppTextEntry::title('name')
                    ->columnSpanFull(),
                AppTextEntry::content()
                    ->columnSpanFull(),
            ]);
    }
}
