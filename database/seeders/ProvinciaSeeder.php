<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Provincia;

/**
 * Seeder de provincias españolas
 * 
 * Crea las 5 provincias principales de España para demostración.
 * En producción, este seeder debería incluir las 52 provincias completas
 * (50 provincias + Ceuta + Melilla).
 * 
 * Utiliza códigos oficiales del INE (Instituto Nacional de Estadística).
 * 
 * @package Database\Seeders
 * @author Cristian Valdivieso
 * @version 1.0
 */
class ProvinciaSeeder extends Seeder
{
    /**
     * Ejecuta el seeder de provincias
     * 
     * Inserta las provincias principales usando códigos INE oficiales.
     * No usa timestamps (tabla maestra de solo lectura).
     * 
     * @return void
     */
    public function run(): void
    {
        Provincia::insert([
            ['codigo' => '28', 'nombre' => 'MADRID'],
            ['codigo' => '08', 'nombre' => 'BARCELONA'],
            ['codigo' => '41', 'nombre' => 'SEVILLA'],
            ['codigo' => '46', 'nombre' => 'VALENCIA'],
            ['codigo' => '29', 'nombre' => 'MÁLAGA'],
        ]);

        $this->command->info('✅ Provincias creadas correctamente');
    }
}