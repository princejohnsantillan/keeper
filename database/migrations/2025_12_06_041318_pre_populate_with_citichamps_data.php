<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $keeper = \App\Models\Keeper::create([
            'first_name' => 'Prince John',
            'middle_name' => 'Jainar',
            'last_name' => 'Santillan',
            'email' => 'prince@keeper.kids',
            'gender' => true,
            'birth_date' => '1988-08-12',
            'phone' => '+639399308514',
        ]);

        $user = \App\Models\User::create([
            'keeper_id' => $keeper->id,
            'name' => $keeper->first_name,
            'email' => $keeper->email,
            'email_verified_at' => now(),
            'password' => bcrypt('secret'),
        ]);

        $organization = \App\Models\Organization::create([
            'name' => 'Citichamps',
            'slug' => 'citichamps',
            'owner_id' => $user->id,
        ]);

        \App\Models\OrganizationUser::create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'role' => 'admin',
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
