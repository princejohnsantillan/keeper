<?php

declare(strict_types=1);

namespace App\Http\Controllers\Keeper;

use App\Actions\WalkInRegistrationAction;
use App\Enums\Gender;
use App\Enums\Relationship;
use App\Filament\Notifications\AppNotification;
use App\Http\Controllers\Controller;
use App\Http\Requests\Keeper\StoreWalkInRegistrationRequest;
use App\Models\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class WalkInRegistrationController extends Controller
{
    public function create(Activity $activity): View
    {
        return view('keeper.walk-ins.create', [
            'activity' => $activity,
            'genderOptions' => collect(Gender::cases())
                ->mapWithKeys(fn (Gender $gender): array => [(string) $gender->value => $gender->getLabel()])
                ->all(),
            'relationshipOptions' => collect(Relationship::cases())
                ->mapWithKeys(fn (Relationship $relationship): array => [$relationship->value => $relationship->getLabel()])
                ->all(),
        ]);
    }

    public function store(
        StoreWalkInRegistrationRequest $request,
        Activity $activity,
        WalkInRegistrationAction $walkInRegistration,
    ): RedirectResponse {
        $validated = $request->validated();

        $guardianData = [
            'first_name' => $validated['guardian_first_name'],
            'middle_name' => $validated['guardian_middle_name'] ?? null,
            'last_name' => $validated['guardian_last_name'],
            'birth_date' => $validated['guardian_birth_date'] ?? null,
            'gender' => $validated['guardian_gender'],
            'email' => $validated['guardian_email'],
            'phone' => $validated['guardian_phone'] ?? null,
        ];

        $childData = [
            'first_name' => $validated['child_first_name'],
            'middle_name' => $validated['child_middle_name'] ?? null,
            'last_name' => $validated['child_last_name'],
            'nickname' => $validated['child_nickname'] ?? null,
            'birth_date' => $validated['child_birth_date'],
            'gender' => $validated['child_gender'],
            'notes' => $validated['child_notes'] ?? null,
        ];

        $relationshipValue = $validated['relationship'];
        $relationship = $relationshipValue instanceof Relationship
            ? $relationshipValue
            : Relationship::from((string) $relationshipValue);
        $agreedToTerms = (bool) ($validated['agree_to_terms'] ?? false);

        $walkInRegistration(
            $guardianData,
            $childData,
            $relationship,
            $activity,
            $agreedToTerms,
            $request->ip(),
            $request->userAgent(),
        );

        AppNotification::walkInRegistered()->send();

        return redirect()
            ->route('keeper.walk-ins.create', ['activity' => $activity])
            ->with('status', 'Walk-in registered successfully.');
    }
}
