<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Propiedad;
use App\Models\UnidadConstructiva;

class UnidadConstructivaSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener todas las propiedades
        $propiedades = Propiedad::all();

        if ($propiedades->isEmpty()) {
            $this->command->warn('No hay propiedades para añadir unidades constructivas.');
            return;
        }

        foreach ($propiedades as $propiedad) {
            // Unidad principal (vivienda)
            UnidadConstructiva::create([
                'propiedad_id' => $propiedad->id,
                'tipo_unidad' => 'VIVIENDA',
                'superficie_m2' => $propiedad->superficie_m2 * 0.85,
                'tipologia' => 'VIVIENDA COLECTIVA',
            ]);

            // Elementos comunes
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