<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Provincia;

class ProvinciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Provincia::insert([
        ['codigo'=>'28', 'nombre'=>'Madrid'],
        ['codigo'=>'08','nombre'=>'Barcelona'],
        ['codigo'=>'41', 'nombre'=>'Sevilla'],
        ['codigo'=>'46', 'nombre'=>'Valencia'],
        ['codigo'=>'29', 'nombre'=>'Málaga'],
        ]);

        $this->command->info(string: 'Provincias de ejemplo creadas correctamente');
    }
}
