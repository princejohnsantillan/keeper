<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\KeeperInvitations;

use App\Actions\GetCurrentKeeperAction;
use App\Filament\Panels\Keeper\Resources\KeeperInvitations\Pages\ListKeeperInvitations;
use App\Filament\Panels\Keeper\Resources\KeeperInvitations\Schemas\KeeperInvitationForm;
use App\Filament\Panels\Keeper\Resources\KeeperInvitations\Tables\KeeperInvitationsTable;
use App\Models\KeeperInvitation;
use App\Subdomain;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class KeeperInvitationResource extends Resource
{
    protected static ?string $model = KeeperInvitation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Pending Invitations';

    protected static ?int $navigationSort = 100;

    public static function canViewAny(): bool
    {
        $currentKeeper = app(GetCurrentKeeperAction::class)->__invoke();

        return $currentKeeper?->isAdmin() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        $organization = Subdomain::organization();

        return parent::getEloquentQuery()
            ->where('organization_id', $organization?->id)
            ->pending()
            ->with(['user', 'invitedBy']);
    }

    public static function form(Schema $schema): Schema
    {
        return KeeperInvitationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KeeperInvitationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKeeperInvitations::route('/'),
        ];
    }
}
