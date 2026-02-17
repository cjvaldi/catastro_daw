<?php
// Para inicialicar las pruebas en desarrollo

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Provincia;
use App\Models\Municipio;
use App\Models\User;
use App\Models\Propiedad;
use App\Models\UnidadConstructiva;
use App\Models\Favorito;
use App\Models\Nota;

class CatastroSeeder extends Seeder
{
    public function run(): void
    {
        // PROVINCIAS
        Provincia::insert([
            ['codigo' => '28', 'nombre' => 'Madrid'],
            ['codigo' => '08', 'nombre' => 'Barcelona'],
            ['codigo' => '41', 'nombre' => 'Sevilla'],
            ['codigo' => '46', 'nombre' => 'Valencia'],
            ['codigo' => '29', 'nombre' => 'Málaga'],
        ]);

        // MUNICIPIOS
        Municipio::insert([
            ['codigo' => '28079', 'nombre' => 'Madrid', 'provincia_codigo' => '28'],
            ['codigo' => '08198', 'nombre' => 'Barcelona', 'provincia_codigo' => '08'],
            ['codigo' => '41091', 'nombre' => 'Sevilla', 'provincia_codigo' => '41'],
            ['codigo' => '46250', 'nombre' => 'Valencia', 'provincia_codigo' => '46'],
            ['codigo' => '29067', 'nombre' => 'Málaga', 'provincia_codigo' => '29'],
        ]);

        // USUARIOS
        User::create([
            'name'=>'Admin Principal',
            'email'=>'admin@catastro.es',
            'password'=>Hash::make('admin123'),
            'rol'=>User::ROLE_ADMIN,
        ]);
(
        User::create([
            'name'=>'Juan Perez',
            'email'=>'juan.perez@email.com',
            'password'=>Hash::make('password'),
            'rol'=>User::ROLE_REGISTRADO,
        ]));

        User::create([
            'name'=>'User Registrado',
            'email'=>'registrado@email.com',
            'password'=>Hash::make('password'),
            'rol'=>User::ROLE_REGISTRADO,
        ]);

        User::create([
            'name'=>'Carlos López',
            'email'=>'carlos.lópez@email.com',
            'password'=>Hash::make('password'),
            'rol'=>User::ROLE_VISITANTE,
        ]);

        User::create([
            'name'=>'User Visitante',
            'email'=>'visitante@email.com',
            'password'=>Hash::make('password'),
            'rol'=>User::ROLE_VISITANTE,
        ]);

        // PROPIEDAD DE PRUEBA
        // $propiedad = Propiedad::create([
        //     'referencia_catastral' => 'TEST1234567890001AA',
        //     'clase' => 'UR',
        //     'provincia_codigo' => '28',
        //     'municipio_codigo' => '28079',
        //     'provincia' => 'Madrid',
        //     'municipio' => 'Madrid',
        //     'direccion_text' => 'Calle Gran Vía 123',
        //     'uso' => 'Residencial',
        //     'superficie_m2' => 85.50,
        //     'coef_participacion' => 1.0000,
        //     'antiguedad_anios' => 25
        // ]);

        // UnidadConstructiva::create([
        //     'propiedad_id' => $propiedad->id,
        //     'tipo_unidad' => 'Vivienda',
        //     'tipologia' => 'Piso',
        //     'superficie_m2' => 85.50
        // ]);

        // Favorito::create([
        //     'usuario_id' => $registrado->id,
        //     'propiedad_id' => $propiedad->id,
        //     'etiqueta' => 'Casa centro Madrid'
        // ]);

        // Nota::create([
        //     'usuario_id' => $registrado->id,
        //     'propiedad_id' => $propiedad->id,
        //     'texto' => 'Propiedad interesante',
        //     'tipo' => 'privada'
        // ]);
    }
}
