<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Invitations\Schemas;

use App\Filament\Components\Forms\AppTextarea;
use App\Filament\Components\Forms\AppTextInput;
use App\Models\Invitation;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

final class InvitationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::code(),
                self::activitySelect()
                    ->columnSpanFull(),
                AppTextInput::firstName('invitee_fullname', 'Invitee Full Name')
                    ->rules(['max:160']),
                AppTextInput::email('invitee_email', 'Invitee Email'),
                self::phoneInput(),
                self::messageSelect()
                    ->columnSpanFull(),
                AppTextarea::notes()
                    ->columnSpanFull(),
            ])->columns(2);
    }

    private static function code(): TextInput
    {
        return TextInput::make('code')
            ->label('Invitation Code')
            ->disabled()
            ->dehydrated(false)
            ->copyable()
            ->hidden(fn (?Invitation $record): bool => $record === null)
            ->columnSpanFull();
    }

    private static function activitySelect(): Select
    {
        return Select::make('activity_id')
            ->label('Activity')
            ->relationship(
                name: 'activity',
                titleAttribute: 'title',
                modifyQueryUsing: fn (Builder $query): Builder => $query->where('is_private', true),
            )
            ->searchable()
            ->preload()
            ->required();
    }

    private static function phoneInput(): TextInput
    {
        return TextInput::make('invitee_phone')
            ->label('Invitee Phone')
            ->tel()
            ->rules(['nullable', 'regex:/^(\+63|0)\d{10}$/'])
            ->helperText('Philippine phone number (e.g., +639171234567 or 09171234567)');
    }

    private static function messageSelect(): Select
    {
        return Select::make('message_id')
            ->label('Message')
            ->relationship(
                name: 'message',
                titleAttribute: 'name',
                modifyQueryUsing: fn (Builder $query): Builder => $query->whereNull('archived_at'),
            )
            ->searchable()
            ->preload()
            ->placeholder('Select a message template...')
            ->helperText('This message will be included in the invitation email.');
    }
}
