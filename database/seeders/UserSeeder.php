<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@catastro.test',
            'password' => Hash::make('Admin1234!'),
            'rol'      => User::ROLE_ADMIN,
            'activo'   => true,
        ]);

        // Registrado (Premium)
        User::create([
            'name'     => 'Usuario Premium',
            'email'    => 'premium@catastro.test',
            'password' => Hash::make('Premium1234!'),
            'rol'      => User::ROLE_REGISTRADO,
            'activo'   => true,
        ]);

        // Visitante (Free)
        User::create([
            'name'     => 'Usuario Visitante',
            'email'    => 'visitante@catastro.test',
            'password' => Hash::make('Visitante1234!'),
            'rol'      => User::ROLE_VISITANTE,
            'activo'   => true,
        ]);

        $this->command->info('Usuarios de ejemplo creados correctamente');
    }
}
