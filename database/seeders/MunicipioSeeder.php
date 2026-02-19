<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Municipio;

/**
 * Seeder de municipios españoles
 * 
 * Crea los 5 municipios capitales principales para demostración.
 * Utiliza códigos oficiales del INE de 5 dígitos (provincia + municipio).
 * 
 * En producción, este seeder incluiría los 8,000+ municipios españoles.
 * El sistema usa firstOrCreate() para crear municipios adicionales
 * automáticamente cuando se guardan propiedades.
 * 
 * @package Database\Seeders
 * @author Cristian Valdivieso
 * @version 1.0
 */
class MunicipioSeeder extends Seeder
{
    /**
     * Ejecuta el seeder de municipios
     * 
     * Inserta los municipios capitales principales con sus códigos INE.
     * Cada código combina: código provincia (2 dígitos) + código municipio (3 dígitos).
     * 
     * @return void
     */
    public function run(): void
    {
        Municipio::insert([
            ['codigo' => '28079', 'nombre' => 'MADRID', 'provincia_codigo' => '28'],
            ['codigo' => '08019', 'nombre' => 'BARCELONA', 'provincia_codigo' => '08'],
            ['codigo' => '41091', 'nombre' => 'SEVILLA', 'provincia_codigo' => '41'],
            ['codigo' => '46250', 'nombre' => 'VALENCIA', 'provincia_codigo' => '46'],
            ['codigo' => '29067', 'nombre' => 'MÁLAGA', 'provincia_codigo' => '29'],
            
            // Municipios adicionales para ejemplos
            ['codigo' => '46138', 'nombre' => 'GODELLETA', 'provincia_codigo' => '46'],
            ['codigo' => '41087', 'nombre' => 'SAN JUAN DE AZNALFARACHE', 'provincia_codigo' => '41'],
        ]);

        $this->command->info('✅ Municipios creados correctamente');
    }
}