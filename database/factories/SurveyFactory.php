<?php

namespace Database\Factories;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Survey>
 */
class SurveyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amountReceived = fake()->randomFloat(2, 1000, 20000);
        $expenses = [
            'expense_food' => fake()->randomFloat(2, 0, $amountReceived * 0.3),
            'expense_educational' => fake()->randomFloat(2, 0, 500),
            'expense_house_rental' => fake()->randomFloat(2, 0, 1000),
            'expense_livelihood' => fake()->randomFloat(2, 0, 2000),
            'expense_medical' => fake()->randomFloat(2, 0, 1000),
            'expense_non_food_items' => fake()->randomFloat(2, 0, 500),
            'expense_utilities' => fake()->randomFloat(2, 0, 500),
            'expense_shelter_materials' => fake()->randomFloat(2, 0, 1000),
            'expense_transportation' => fake()->randomFloat(2, 0, 500),
            'expense_others' => fake()->randomFloat(2, 0, 300),
        ];

        $totalUtilization = array_sum($expenses);
        $variance = $amountReceived - $totalUtilization;

        return [
            'user_id' => User::factory(),
            'incident_id' => Incident::factory(),
            'client_uuid' => Str::uuid()->toString(),
            'consent_agreed' => true,
            'beneficiary_name' => fake()->name(),
            'respondent_name' => fake()->name(),
            'relationship_to_beneficiary' => fake()->randomElement(['Self', 'Spouse', 'Child', 'Parent', 'Others']),
            'relationship_specify' => null,
            'birthdate' => fake()->date(),
            'age' => fake()->numberBetween(18, 80),
            'beneficiary_classification' => [fake()->randomElement(['4Ps', 'Non-4Ps', 'IP', 'Senior Citizen'])],
            'household_id_no' => fake()->numerify('########'),
            'sex' => fake()->randomElement(['Male', 'Female']),
            'demographic_classification' => [fake()->randomElement(['Indigenous People', 'Senior Citizen', 'PWD', 'Solo Parent', 'None'])],
            'ip_specify' => null,
            'highest_educational_attainment' => fake()->randomElement(['Elementary', 'High School', 'College', 'Vocational']),
            'educational_attainment_specify' => null,
            'province' => fake()->randomElement(['Agusan del Norte', 'Agusan del Sur', 'Surigao del Norte', 'Surigao del Sur', 'Dinagat Islands']),
            'district' => 'District 1',
            'municipality' => fake()->city(),
            'barangay' => 'Barangay '.fake()->numberBetween(1, 50),
            'sitio_purok_street' => fake()->optional()->streetName(),
            'latitude' => fake()->latitude(8.0, 10.0),
            'longitude' => fake()->longitude(125.0, 126.5),
            'altitude' => fake()->optional()->randomFloat(2, 0, 500),
            'accuracy' => fake()->optional()->randomFloat(2, 1, 50),
            'utilization_type' => fake()->randomElement(['Relief/Response', 'Recovery/Rehabilitation']),
            'amount_received' => $amountReceived,
            'date_received' => fake()->dateTimeBetween('-6 months', 'now'),
            ...$expenses,
            'total_utilization' => $totalUtilization,
            'unutilized_variance' => $variance,
            'livelihood_types' => [fake()->randomElement(['Farming', 'Fishing', 'Vending', 'Others'])],
            'livelihood_specify' => null,
            'expense_others_specify' => null,
            'reason_not_fully_utilized' => $variance > 0 ? fake()->sentence() : null,
            'interviewed_by' => fake()->name(),
            'position' => fake()->randomElement(['Social Worker', 'Field Officer', 'Enumerator']),
            'survey_modality' => fake()->randomElement(['Face-to-face', 'Phone', 'Others']),
            'modality_specify' => null,
        ];
    }
}
