<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Municipio;

class MunicipioSeeder extends Seeder
{
    public function run(): void
    {
        $url = 'https://raw.githubusercontent.com/marcovega/colombia-json/master/colombia.json';
        $response = Http::get($url);

        if ($response->successful()) {
            foreach ($response->json() as $item) {
                foreach ($item['ciudades'] as $ciudad) {
                    Municipio::firstOrCreate([
                        'nombre' => $ciudad,
                        'departamento' => $item['departamento']
                    ]);
                }
            }
        }
    }
}
