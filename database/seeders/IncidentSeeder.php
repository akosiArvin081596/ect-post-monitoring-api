<?php

namespace Database\Seeders;

use App\Models\Incident;
use Illuminate\Database\Seeder;

class IncidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $incidents = [
            [
                'name' => 'ECT Monitoring for Typhoon Tino',
                'type' => 'Typhoon',
                'starts_at' => now()->subDays(7)->toDateString(),
                'ends_at' => null,
                'is_active' => true,
                'description' => 'Rapid monitoring following Typhoon Tino.',
            ],
            [
                'name' => 'ECT Monitoring for Earthquake East Ridge',
                'type' => 'Earthquake',
                'starts_at' => now()->subDays(30)->toDateString(),
                'ends_at' => now()->subDays(15)->toDateString(),
                'is_active' => false,
                'description' => 'Closed incident for East Ridge earthquake response.',
            ],
        ];

        foreach ($incidents as $incident) {
            Incident::query()->firstOrCreate(
                ['name' => $incident['name']],
                $incident
            );
        }
    }
}
