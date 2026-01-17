<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\AuthUser;
use App\Filament\Notifications\AppNotification;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Guardian;
use App\Services\Contracts\GatepassServiceInterface;
use App\Services\Contracts\TermAcceptanceServiceInterface;
use Filament\Actions\Action;

final class RequestGatePassAction
{
    public static function make(Activity $activity, ?string $name = 'requestGatePass', string $label = 'Request'): Action
    {
        return Action::make($name)->label($label)
            ->button()
            ->color('primary')
            ->hidden(fn (callable $get): bool => ! empty($get('gatepass_code')))
            ->action(function (
                callable $get,
                callable $set,
                TermAcceptanceServiceInterface $termAcceptanceService,
                GatepassServiceInterface $gatepassService,
            ) use ($activity): void {
                if ($activity->term !== null && ! $get('../../agree_to_terms')) {
                    AppNotification::termsNotAgreed()->send();

                    return;
                }

                $childId = $get('child_id');
                $guardianId = $get('guardian_id');

                if (empty($guardianId)) {
                    return;
                }

                $requestingGuardian = AuthUser::guardian();
                /** @var Child|null $child */
                $child = Child::query()->find($childId);
                /** @var Guardian|null $checkinGuardian */
                $checkinGuardian = Guardian::query()->find($guardianId);

                if ($child === null || $checkinGuardian === null) {
                    return;
                }

                $termAcceptance = null;
                if ($activity->term !== null) {
                    $termAcceptance = $termAcceptanceService->getAcceptance(
                        $activity->term,
                        $requestingGuardian
                    );
                }

                $gatepass = $gatepassService->create(
                    $activity,
                    $child,
                    $checkinGuardian,
                    $termAcceptance
                );

                $set('gatepass_code', $gatepass->code);
                $set('../../terms_locked', true);

                AppNotification::registeredToActivity()->send();
            });
    }
}
