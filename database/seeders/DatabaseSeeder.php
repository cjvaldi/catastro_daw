<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder principal de la base de datos
 * 
 * Orquesta la ejecución de todos los seeders en el orden correcto
 * para poblar la base de datos con datos de prueba y demostración.
 * 
 * Orden de ejecución (importante para mantener integridad referencial):
 * 1. Tablas maestras (Provincias, Municipios)
 * 2. Usuarios (necesarios para propiedades)
 * 3. Propiedades (necesarias para favoritos, notas y unidades)
 * 4. Relaciones y dependencias (Unidades, Favoritos, Notas)
 * 
 * Uso:
 * - php artisan db:seed (ejecuta todos los seeders)
 * - php artisan migrate:fresh --seed (resetea BD y ejecuta seeders)
 * 
 * Datos generados:
 * - 5 provincias principales
 * - 7 municipios (5 capitales + 2 adicionales)
 * - 3 usuarios (admin, premium, visitante)
 * - 4 propiedades de ejemplo
 * - 8 unidades constructivas (2 por propiedad)
 * - 1 favorito de ejemplo
 * - 2 notas de ejemplo (1 privada + 1 pública)
 * 
 * @package Database\Seeders
 * @author Cristian Valdivieso
 * @version 1.0
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Ejecuta todos los seeders del proyecto
     * 
     * Llama a cada seeder en el orden correcto para garantizar que
     * las relaciones de clave foránea se satisfagan correctamente.
     * 
     * IMPORTANTE: Mantener este orden:
     * 1. Primero: Tablas sin dependencias (provincias, municipios)
     * 2. Segundo: Usuarios (solo dependen de sí mismos)
     * 3. Tercero: Propiedades (dependen de usuarios, provincias, municipios)
     * 4. Cuarto: Relaciones (dependen de propiedades)
     * 
     * @return void
     */
    public function run(): void
    {
        // Seeders para datos de la entrega del proyecto
        $this->call([
            ProvinciaSeeder::class,           // 1. Tablas maestras geográficas
            MunicipioSeeder::class,           // 2. Municipios (dependen de provincias)
            UserSeeder::class,                // 3. Usuarios de prueba
            PropiedadSeeder::class,           // 4. Propiedades (dependen de usuarios)
            UnidadConstructivaSeeder::class,  // 5. Unidades (dependen de propiedades)
            FavoritoSeeder::class,            // 6. Favoritos (dependen de propiedades)
            NotaSeeder::class,                // 7. Notas (dependen de propiedades)
        ]);

        // Mensaje de confirmación
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info(' Base de datos poblada correctamente');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info(' DATOS CREADOS:');
        $this->command->info('   • 5 Provincias');
        $this->command->info('   • 7 Municipios');
        $this->command->info('   • 3 Usuarios de prueba');
        $this->command->info('   • 4 Propiedades de ejemplo');
        $this->command->info('   • 8 Unidades constructivas');
        $this->command->info('   • 1 Favorito');
        $this->command->info('   • 2 Notas');
        $this->command->info('');
        $this->command->info(' CREDENCIALES DE ACCESO:');
        $this->command->info('   Admin:     admin@catastro.test / Admin1234!');
        $this->command->info('   Premium:   premium@catastro.test / Premium1234!');
        $this->command->info('   Visitante: visitante@catastro.test / Visitante1234!');
        $this->command->info('');
    }
}