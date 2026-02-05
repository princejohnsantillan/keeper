<?php

use App\Enums\Gender;
use App\Enums\KeeperRole;
use App\Models\Guardian;
use App\Models\Keeper;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $user = User::create([
            'name' => 'Prince',
            'email' => 'prince@keeper.kids',
            'email_verified_at' => now(),
            'password' => bcrypt('secret'),
        ]);

        $guardian = Guardian::create([
            'first_name' => 'Prince John',
            'middle_name' => 'Jainar',
            'last_name' => 'Santillan',
            'email' => $user->email,
            'gender' => Gender::Male,
            'birth_date' => '1988-08-12',
            'phone' => '+639399308514',
            'owner_id' => $user->id,
        ]);

        $user->update(['guardian_id' => $guardian->id]);

        $organization = Organization::create([
            'name' => 'Citichurch',
            'slug' => 'citichurch',
            'owner_id' => $user->id,
        ]);

        // Keeper is automatically created by OrganizationObserver

        $user2 = User::create([
            'name' => 'Pearl  Anne',
            'email' => 'pearlannelao@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('secret'),
        ]);

        $guardian2 = Guardian::create([
            'first_name' => 'Pearl',
            'middle_name' => 'Ocenar',
            'last_name' => 'Lao',
            'email' => $user2->email,
            'gender' => Gender::Female,
            'birth_date' => '2025-01-01',
            'phone' => '+639177123519',
            'owner_id' => $user2->id,
        ]);

        $user2->update(['guardian_id' => $guardian2->id]);

        $organization2 = Organization::create([
            'name' => 'Citichamps',
            'slug' => 'citichamps',
            'owner_id' => $user->id,
        ]);

        // Owner's keeper is automatically created by OrganizationObserver
        // Add user2 as an additional keeper
        Keeper::create([
            'user_id' => $user2->id,
            'organization_id' => $organization2->id,
            'role' => KeeperRole::Gatekeeper,
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
