<?php

namespace Database\Seeders;

use  App\Models\User;
use App\Models\Propiedad;
use App\Models\UnidadConstructiva;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropiedadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // PROPIEDAD DE PRUEBA
        $usuario1 = User::where('email', 'juan.perez@email.com')->first();
        $usuario2 = User::where('email', 'maria.garcia@email.com')->first();

        Propiedad::create([
            'user_id' => $usuario1->id,
            'referencia_catastral' => 'TEST1234567890001AA',
            'clase' => 'UR',
            'provincia_codigo' => '28',
            'municipio_codigo' => '28079',
            'provincia' => 'Madrid',
            'municipio' => 'Madrid',
            'direccion_text' => 'Calle Gran Vía 123',
            'uso' => 'Residencial',
            'superficie_m2' => 85.5,
            'coef_participacion' => 1,
            'antiguedad_anios' => 25,
        ]);

        Propiedad::create([
            'user_id' => $usuario2->id,
            'referencia_catastral' => 'TEST9876543210001BB',
            'clase' => 'UR',
            'provincia_codigo' => '08',
            'municipio_codigo' => '08198',
            'provincia' => 'Barcelona',
            'municipio' => 'Barcelona',
            'direccion_text' => 'Avenida Diagonal 456',
            'uso' => 'Residencial',
            'superficie_m2' => 120.75,
            'coef_participacion' => 1,
            'antiguedad_anios' => 15,
        ]);
    }
}
