<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * Seeder de usuarios de prueba
 * 
 * Crea tres usuarios con diferentes roles para testing y demostración:
 * - Admin: Acceso completo al sistema
 * - Premium: Usuario con funcionalidades avanzadas
 * - Visitante: Usuario gratuito con funciones básicas
 * 
 * Credenciales de acceso:
 * - Admin: admin@catastro.test / Admin1234!
 * - Premium: premium@catastro.test / Premium1234!
 * - Visitante: visitante@catastro.test / Visitante1234!
 * 
 * @package Database\Seeders
 * @author Cristian Valdivieso
 * @version 1.0
 */
class UserSeeder extends Seeder
{
    /**
     * Ejecuta el seeder de usuarios
     * 
     * Crea tres usuarios de prueba, uno por cada rol del sistema.
     * Las contraseñas están hasheadas con Bcrypt para seguridad.
     * Todos los usuarios están activos por defecto.
     * 
     * @return void
     */
    public function run(): void
    {
        // Usuario Administrador - Acceso completo
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@catastro.test',
            'password' => Hash::make('Admin1234!'),
            'rol'      => User::ROLE_ADMIN,
            'activo'   => true,
        ]);

        // Usuario Premium (Registrado) - Funcionalidades avanzadas
        User::create([
            'name'     => 'Usuario Premium',
            'email'    => 'premium@catastro.test',
            'password' => Hash::make('Premium1234!'),
            'rol'      => User::ROLE_REGISTRADO,
            'activo'   => true,
        ]);

        // Usuario Visitante (Free) - Funcionalidades básicas
        User::create([
            'name'     => 'Usuario Visitante',
            'email'    => 'visitante@catastro.test',
            'password' => Hash::make('Visitante1234!'),
            'rol'      => User::ROLE_VISITANTE,
            'activo'   => true,
        ]);

        $this->command->info('✅ Usuarios de ejemplo creados correctamente');
    }
}