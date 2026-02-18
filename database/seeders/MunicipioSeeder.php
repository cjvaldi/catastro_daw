<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Municipio;

class MunicipioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Municipio::insert([
            // Capitales principales
            ['codigo' => '28079', 'nombre' => 'MADRID', 'provincia_codigo' => '28'],
            ['codigo' => '08019', 'nombre' => 'BARCELONA', 'provincia_codigo' => '08'],
            ['codigo' => '41091', 'nombre' => 'SEVILLA', 'provincia_codigo' => '41'],
            ['codigo' => '46250', 'nombre' => 'VALENCIA', 'provincia_codigo' => '46'],
            ['codigo' => '29067', 'nombre' => 'MÁLAGA', 'provincia_codigo' => '29'],

            // Municipios adicionales para ejemplos
            ['codigo' => '46138', 'nombre' => 'GODELLETA', 'provincia_codigo' => '46'],
            ['codigo' => '41087', 'nombre' => 'SAN JUAN DE AZNALFARACHE', 'provincia_codigo' => '41'],
        ]);

        $this->command->info('Municipios de ejemplo creadas correctamente');
    }
}
