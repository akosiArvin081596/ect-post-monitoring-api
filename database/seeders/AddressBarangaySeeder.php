<?php

namespace Database\Seeders;

use App\Models\AddressBarangay;
use Illuminate\Database\Seeder;

/*
 * Initial barangay seed for the Province of Dinagat Islands (CARAGA, Region XIII).
 * Source: best-effort from publicly-available PSGC-style references.
 *
 * VERIFY against the current PSA/PSGC publication before relying on this list
 * in production. To update: replace the $data array with an authoritative source
 * (typically a PSGC CSV from psa.gov.ph) and re-run:
 *
 *     php artisan db:seed --class=AddressBarangaySeeder --force
 *
 * Other CARAGA provinces (Agusan del Norte, Agusan del Sur, Surigao del Norte,
 * Surigao del Sur) are not yet seeded. The frontend falls back to a free-text
 * input when a municipality has no barangay rows, so omission does not break
 * the form.
 */
class AddressBarangaySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Dinagat Islands' => [
                'Lone District' => [
                    'San Jose' => [
                        'Aurelio (Poblacion)', 'Cuarinta', 'Don Ruben Ecleo Sr. (Justiniana Edera)',
                        'Jacquez', 'Luna', 'Mahayahay', 'Matingbe', 'San Juan',
                        'Santa Cruz', 'Santa Fe', 'Sto. Niño', 'Wadas',
                    ],
                    'Basilisa (Rizal)' => [
                        'Callo', 'Catadman', 'Doña Helene', 'Edera', 'Ferdinand',
                        'Geotina', 'Imelda', 'Magsaysay', 'Matingbe', 'Montag',
                        'Navarro', 'New Mabuhay', 'Old Mabuhay', 'Poblacion',
                        'Rita Village', 'Roxas', 'San Juan', 'Santa Cruz',
                        'Sering', 'Sta. Rita', 'Tag-abaca',
                    ],
                    'Cagdianao' => [
                        'Boa', 'Cabunga-an', 'Del Pilar', 'Laguna', 'Legaspi',
                        'Montag', 'Ngoyap', 'Nueva Estrella', 'R. Ecleo (Poblacion)', 'Tigasao',
                    ],
                    'Dinagat' => [
                        'Cayetano', 'Escolta (Poblacion)', 'Gomez', 'Justiniana Edera',
                        'Magsaysay', 'Mauswagon', 'New Mabuhay', 'Puerto Princesa',
                        'San Jose (Poblacion)', 'San Juan', 'San Pedro', 'White Beach',
                        'Wilson', 'Windflower',
                    ],
                    'Libjo (Albor)' => [
                        'Albor (Poblacion)', 'Ba-ol', 'Binga', 'Dona Helene', 'Garcia',
                        'Jagupit', 'Llamera', 'Magsaysay', 'Panamaon', 'Plaridel',
                        'San Jose', 'Santa Cruz', 'Sering', 'Toytoy',
                    ],
                    'Loreto' => [
                        'Aguinaldo', 'Bagumbayan', 'Esperanza', 'Ferdinand', 'Helene',
                        'Liberty', 'Magsaysay', 'Mahayahay', 'Malinao', 'Panamaon',
                        'Rizal', 'San Jose', 'San Roque (Poblacion)', 'Santiago',
                        'Sta. Cruz', 'Tagbayani',
                    ],
                    'Tubajon' => [
                        'Cambinlia', 'Diaz', 'Imelda', 'Malinao', 'Mabini (Poblacion)',
                        'New Catmon', 'San Roque', 'Sta. Cruz', 'Taligaman', 'Tinago',
                    ],
                ],
            ],
        ];

        $records = [];
        $now = now();

        foreach ($data as $province => $districts) {
            foreach ($districts as $district => $municipalities) {
                foreach ($municipalities as $municipality => $barangays) {
                    foreach ($barangays as $barangay) {
                        $records[] = [
                            'province' => $province,
                            'district' => $district,
                            'municipality' => $municipality,
                            'barangay' => $barangay,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }
        }

        // Idempotent re-seed: clear only the rows this seeder owns, then insert.
        AddressBarangay::query()
            ->where('province', 'Dinagat Islands')
            ->delete();
        AddressBarangay::query()->insert($records);
    }
}
