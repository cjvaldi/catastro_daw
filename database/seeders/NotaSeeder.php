<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Propiedad;
use App\Models\Nota;

class NotaSeeder extends Seeder
{
    public function run(): void
    {
        $premium = User::where('email', 'premium@catastro.test')->first();

        if (!$premium) {
            $this->command->warn('Usuario Premium no encontrado.');
            return;
        }

        // Obtener propiedades del usuario Premium
        $propiedades = Propiedad::where('user_id', $premium->id)->get();

        if ($propiedades->isEmpty()) {
            $this->command->warn('No hay propiedades para añadir notas.');
            return;
        }

        // Nota privada en la primera propiedad
        Nota::create([
            'usuario_id' => $premium->id,
            'propiedad_id' => $propiedades->first()->id,
            'texto' => 'Propiedad muy interesante, ubicada en zona céntrica. Buena inversión.',
            'tipo' => 'privada',
        ]);

        // Nota pública en la segunda propiedad (si existe)
        if ($propiedades->count() > 1) {
            Nota::create([
                'usuario_id' => $premium->id,
                'propiedad_id' => $propiedades->skip(1)->first()->id,
                'texto' => 'Zona con buenas comunicaciones y servicios cercanos.',
                'tipo' => 'publica',
            ]);
        }

        $this->command->info('Notas de ejemplo creadas correctamente');
    }
}