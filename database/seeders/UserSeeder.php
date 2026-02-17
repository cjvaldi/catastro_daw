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
    }
}
