<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Children\Schemas;

use App\Filament\Components\Infolists\AppIconEntry;
use App\Filament\Components\Infolists\AppSpatieMediaLibraryImageEntry;
use App\Filament\Components\Infolists\AppTextEntry;
use Filament\Schemas\Schema;

final class ChildInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                AppSpatieMediaLibraryImageEntry::avatar()
                    ->columnSpanFull(),
                AppTextEntry::firstName(),
                AppTextEntry::middleName(),
                AppTextEntry::lastName(),
                AppTextEntry::nickname(),
                AppTextEntry::birthDate(),
                AppIconEntry::gender(),
                AppTextEntry::notes()
                    ->columnSpanFull(),
                AppTextEntry::createdAt(),
                AppTextEntry::updatedAt(),
            ]);
    }
}
