<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Propiedad;
use App\Models\Favorito;

class FavoritoSeeder extends Seeder
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
            $this->command->warn('No hay propiedades para marcar como favoritas.');
            return;
        }

        // Marcar la primera propiedad como favorita
        Favorito::create([
            'usuario_id' => $premium->id,
            'propiedad_id' => $propiedades->first()->id,
        ]);

        $this->command->info('Favoritos de ejemplo creados correctamente');
    }
}