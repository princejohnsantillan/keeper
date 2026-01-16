<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Organization;
use Illuminate\Database\Seeder;

final class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organization = Organization::query()->where('slug', 'citichurch')->firstOrFail();
        $createdBy = $organization->owner_id;

        dd($createdBy);
        $activities = [
            [
                'title' => 'Sunday Service',
                'description' => 'Weekly Sunday worship service',
                'location' => 'Main Auditorium',
                'starts_at' => now()->next('Sunday')->setTime(9, 0),
                'ends_at' => now()->next('Sunday')->setTime(12, 0),
            ],
            // Add more activities here as needed:
            // [
            //     'title' => 'Activity Title',
            //     'description' => 'Activity description',
            //     'location' => 'Activity location',
            //     'starts_at' => now()->addDays(7)->setTime(10, 0),
            //     'ends_at' => now()->addDays(7)->setTime(12, 0),
            // ],
        ];

        foreach ($activities as $activityData) {
            Activity::query()->create([
                ...$activityData,
                'organization_id' => $organization->id,
                'created_by' => $createdBy,
                'published_at' => now(),
            ]);
        }
    }
}
