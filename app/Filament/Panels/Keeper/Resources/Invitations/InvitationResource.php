<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Invitations;

use App\Actions\GetCurrentKeeperAction;
use App\Filament\Panels\Keeper\Resources\Invitations\Pages\ManageInvitations;
use App\Filament\Panels\Keeper\Resources\Invitations\Schemas\InvitationForm;
use App\Filament\Panels\Keeper\Resources\Invitations\Tables\InvitationsTable;
use App\Models\Invitation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

final class InvitationResource extends Resource
{
    protected static ?string $model = Invitation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Activity';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Invitations';

    public static function canAccess(): bool
    {
        $currentKeeper = app(GetCurrentKeeperAction::class)->__invoke();

        return $currentKeeper->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return InvitationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvitationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageInvitations::route('/'),
        ];
    }
}
