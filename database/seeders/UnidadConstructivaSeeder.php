<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Propiedad;
use App\Models\UnidadConstructiva;

/**
 * Seeder de unidades constructivas de ejemplo
 * 
 * Crea subdivisiones catastrales para cada propiedad de prueba:
 * - Unidad principal (VIVIENDA)
 * - Elementos comunes del edificio
 * 
 * Simula la estructura real de respuesta de la API del Catastro.
 * 
 * @package Database\Seeders
 * @author Cristian Valdivieso
 * @version 1.0
 */
class UnidadConstructivaSeeder extends Seeder
{
    /**
     * Ejecuta el seeder de unidades constructivas
     * 
     * Para cada propiedad existente, crea:
     * 1. Unidad principal (85% de la superficie total)
     * 2. Elementos comunes (15% de la superficie total)
     * 
     * @return void
     */
    public function run(): void
    {
        // Obtener todas las propiedades
        $propiedades = Propiedad::all();

        if ($propiedades->isEmpty()) {
            $this->command->warn('No hay propiedades para añadir unidades constructivas.');
            return;
        }

        foreach ($propiedades as $propiedad) {
            // Unidad principal (vivienda) - 85% de superficie
            UnidadConstructiva::create([
                'propiedad_id' => $propiedad->id,
                'tipo_unidad' => 'VIVIENDA',
                'superficie_m2' => $propiedad->superficie_m2 * 0.85,
                'tipologia' => 'VIVIENDA COLECTIVA',
            ]);

            // Elementos comunes - 15% de superficie
            UnidadConstructiva::create([
                'propiedad_id' => $propiedad->id,
                'tipo_unidad' => 'ELEMENTOS COMUNES',
                'superficie_m2' => $propiedad->superficie_m2 * 0.15,
                'tipologia' => 'N/A',
            ]);
        }

        $this->command->info('Unidades constructivas de ejemplo creadas correctamente');
    }
}