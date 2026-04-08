<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Invitations\Pages;

use App\AuthUser;
use App\Facades\Subdomain;
use App\Filament\Panels\Keeper\Resources\Invitations\InvitationResource;
use App\Models\Invitation;
use App\Services\Contracts\InvitationServiceInterface;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

final class ManageInvitations extends ManageRecords
{
    protected static string $resource = InvitationResource::class;

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver()
                ->createAnother(false)
                ->mutateDataUsing(function (array $data, InvitationServiceInterface $invitationService): array {
                    $organization = Subdomain::organization();
                    $data['organization_id'] = $organization?->id;
                    $data['created_by'] = AuthUser::user()->id;
                    $data['code'] = $invitationService->generateUniqueCode($organization);

                    return $data;
                })
                ->after(function (Invitation $record, InvitationServiceInterface $invitationService): void {
                    $invitationService->sendInvitationEmail($record);
                }),
        ];
    }
}
