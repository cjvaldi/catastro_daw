<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Gestión de la información en desarrollo
        // $this->call(CatastroSeeder::class);

        //Gestión de la información de la entrega del proyecto
        $this->call([
            // ProvinciaSeeder::class,
            // MunicipioSeeder::class,
            UserSeeder::class,
            // PropiedadSeeder::class,
            // UnidadConstructivaSeeder::class,
            // FavoritoSeeder::class,
            // NotaSeeder::class,
        ]);
    }
}
