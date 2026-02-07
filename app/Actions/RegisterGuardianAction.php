<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Guardian;
use App\Models\User;

final class RegisterGuardianAction
{
    public function __construct(
        private CreateOwnershipAction $createOwnershipAction,
    ) {}

    /**
     * Register a new guardian with their associated user account.
     *
     * @param  array<string, mixed>  $data
     */
    public function __invoke(array $data): User
    {
        $user = User::query()->create([
            'name' => trim($data['first_name'].' '.$data['last_name']),
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $guardian = Guardian::query()->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'gender' => $data['gender'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);

        ($this->createOwnershipAction)($guardian, $user);

        $user->update(['guardian_id' => $guardian->id]);

        return $user->refresh();
    }
}
