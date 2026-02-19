<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Propiedad;

/**
 * Seeder de propiedades de ejemplo
 * 
 * Crea propiedades de prueba asociadas a los usuarios Visitante y Premium.
 * Utiliza referencias catastrales reales que funcionan en la API del Catastro
 * y datos simulados para demostración.
 * 
 * Distribución:
 * - Usuario Visitante: 2 propiedades
 * - Usuario Premium: 2 propiedades
 * 
 * Incluye referencias reales verificadas:
 * - 2749704YJ0624N0001DI (Godelleta, Valencia)
 * - 3301204QB6430S0008QR (San Juan de Aznalfarache, Sevilla)
 * 
 * @package Database\Seeders
 * @author Cristian Valdivieso
 * @version 1.0
 */
class PropiedadSeeder extends Seeder
{
    /**
     * Ejecuta el seeder de propiedades
     * 
     * Crea 4 propiedades de ejemplo distribuidas entre usuarios Visitante y Premium.
     * Verifica que los usuarios existan antes de crear las propiedades.
     * 
     * @return void
     */
    public function run(): void
    {
        // Obtener usuarios de prueba
        $visitante = User::where('email', 'visitante@catastro.test')->first();
        $premium = User::where('email', 'premium@catastro.test')->first();

        // Verificar que existan los usuarios
        if (!$visitante || !$premium) {
            $this->command->warn('⚠️ Usuarios no encontrados. Ejecuta UserSeeder primero.');
            return;
        }

        // PROPIEDAD 1 - Visitante (Valencia - Referencia real)
        Propiedad::create([
            'user_id' => $visitante->id,
            'referencia_catastral' => '2749704YJ0624N0001DI',
            'clase' => 'UR',
            'provincia_codigo' => '46',
            'municipio_codigo' => '46138',
            'provincia' => 'VALENCIA',
            'municipio' => 'GODELLETA',
            'direccion_text' => 'CL GUAYANA-MOJONERA 3',
            'tipo_via' => 'CL',
            'nombre_via' => 'GUAYANA-MOJONERA',
            'numero' => '3',
            'escalera' => 'T',
            'planta' => 'OD',
            'puerta' => 'OS',
            'codigo_postal' => '46388',
            'uso' => 'Residencial',
            'superficie_m2' => 57.00,
            'coef_participacion' => 1.00,
            'antiguedad_anios' => 49,
            'raw_json' => null,
        ]);

        // PROPIEDAD 2 - Visitante (Barcelona - Ejemplo)
        Propiedad::create([
            'user_id' => $visitante->id,
            'referencia_catastral' => 'TEST0001BARCELONA01',
            'clase' => 'UR',
            'provincia_codigo' => '08',
            'municipio_codigo' => '08019',
            'provincia' => 'BARCELONA',
            'municipio' => 'BARCELONA',
            'direccion_text' => 'AV DIAGONAL 456',
            'tipo_via' => 'AV',
            'nombre_via' => 'DIAGONAL',
            'numero' => '456',
            'escalera' => 'A',
            'planta' => '03',
            'puerta' => 'B',
            'codigo_postal' => '08013',
            'uso' => 'Residencial',
            'superficie_m2' => 95.50,
            'coef_participacion' => 1.00,
            'antiguedad_anios' => 30,
            'raw_json' => null,
        ]);

        // PROPIEDAD 3 - Premium (Madrid - Ejemplo)
        Propiedad::create([
            'user_id' => $premium->id,
            'referencia_catastral' => 'TEST0002MADRID00001',
            'clase' => 'UR',
            'provincia_codigo' => '28',
            'municipio_codigo' => '28079',
            'provincia' => 'MADRID',
            'municipio' => 'MADRID',
            'direccion_text' => 'CL GRAN VIA 123',
            'tipo_via' => 'CL',
            'nombre_via' => 'GRAN VIA',
            'numero' => '123',
            'escalera' => 'B',
            'planta' => '05',
            'puerta' => 'A',
            'codigo_postal' => '28013',
            'uso' => 'Residencial',
            'superficie_m2' => 120.75,
            'coef_participacion' => 1.00,
            'antiguedad_anios' => 40,
            'raw_json' => null,
        ]);

        // PROPIEDAD 4 - Premium (Sevilla - Referencia real)
        Propiedad::create([
            'user_id' => $premium->id,
            'referencia_catastral' => '3301204QB6430S0008QR',
            'clase' => 'UR',
            'provincia_codigo' => '41',
            'municipio_codigo' => '41087',
            'provincia' => 'SEVILLA',
            'municipio' => 'SAN JUAN DE AZNALFARACHE',
            'direccion_text' => 'CL BRIHUEGA 6',
            'tipo_via' => 'CL',
            'nombre_via' => 'BRIHUEGA',
            'numero' => '6',
            'escalera' => null,
            'planta' => null,
            'puerta' => null,
            'codigo_postal' => '41920',
            'uso' => 'Residencial',
            'superficie_m2' => 100.00,
            'coef_participacion' => 1.00,
            'antiguedad_anios' => 46,
            'raw_json' => null,
        ]);

        $this->command->info('✅ Propiedades de ejemplo creadas correctamente');
    }
}