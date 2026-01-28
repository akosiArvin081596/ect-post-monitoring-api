<?php

namespace Database\Seeders;

use App\Models\AddressMunicipality;
use Illuminate\Database\Seeder;

class AddressMunicipalitySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Agusan del Norte' => [
                'District 1' => ['Buenavista', 'Carmen', 'Jabonga', 'Kitcharao', 'Las Nieves', 'Magallanes', 'Nasipit', 'Santiago', 'Tubay'],
                'District 2' => ['Butuan City', 'Cabadbaran City', 'Remedios T. Romualdez'],
            ],
            'Agusan del Sur' => [
                'District 1' => ['Bayugan City', 'Sibagat', 'Prosperidad', 'Trento', 'Rosario'],
                'District 2' => ['Bunawan', 'Loreto', 'La Paz', 'San Luis', 'Santa Josefa', 'Talacogon', 'Veruela', 'San Francisco'],
            ],
            'Surigao del Norte' => [
                'District 1' => ['Surigao City', 'San Francisco (Anao-aon)', 'Sison', 'Tagana-an', 'Mainit', 'Alegria', 'Bacuag', 'Claver', 'Gigaquit', 'Tubod', 'Placer', 'Malimono'],
                'District 2' => ['Dapa', 'Del Carmen', 'General Luna', 'Pilar', 'San Benito', 'San Isidro', 'Santa Monica', 'Socorro', 'Burgos'],
            ],
            'Surigao del Sur' => [
                'District 1' => ['Tandag City', 'Bislig City', 'Cagwait', 'Marihatag', 'San Agustin', 'Barobo', 'Hinatuan', 'Lingig', 'Tagbina', 'Tago'],
                'District 2' => ['Cantilan', 'Carrascal', 'Carmen', 'Cortes', 'Lanuza', 'Lianga', 'Madrid', 'San Miguel'],
            ],
            'Dinagat Islands' => [
                'Lone District' => ['San Jose', 'Basilisa (Rizal)', 'Cagdianao', 'Dinagat', 'Libjo (Albor)', 'Loreto', 'Tubajon'],
            ],
        ];

        $records = [];
        $now = now();

        foreach ($data as $province => $districts) {
            foreach ($districts as $district => $municipalities) {
                foreach ($municipalities as $municipality) {
                    $records[] = [
                        'province' => $province,
                        'district' => $district,
                        'municipality' => $municipality,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        AddressMunicipality::query()->truncate();
        AddressMunicipality::query()->insert($records);
    }
}
