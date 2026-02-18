<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropiedadController;
use App\Http\Controllers\UpgradeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas — Anónimo
|--------------------------------------------------------------------------
*/
Route::get('/manual', function () {
    return view('manual');
})->name('manual');

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::post('/propiedades/buscar', [PropiedadController::class, 'buscar'])
    ->name('propiedades.buscar');

Route::get('/propiedades', [PropiedadController::class, 'index'])
    ->name('propiedades.index');

/*
|--------------------------------------------------------------------------
| Visitante + Registrado + Admin — Requiere login
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'activo'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // Ver detalle de propiedad
    Route::get('/propiedades/{propiedad}', [PropiedadController::class, 'show'])
        ->name('propiedades.show');

    // Historial
    Route::get('/historial', [PropiedadController::class, 'historial'])
        ->name('propiedades.historial');

    // Upgrade a Premium
    Route::get('/upgrade', [UpgradeController::class, 'show'])
        ->name('upgrade.show');
    Route::post('/upgrade', [UpgradeController::class, 'upgrade'])
        ->name('upgrade.process');
});

/*
|--------------------------------------------------------------------------
| Solo Registrado (Premium) + Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'activo', 'role:registrado,admin'])->group(function () {

    // Búsqueda por dirección
    Route::get('/propiedades/buscar-direccion', function () {
        return view('propiedades.buscar-direccion');
    })->name('propiedades.formBuscarDireccion');

    Route::post('/propiedades/buscar-direccion',
        [PropiedadController::class, 'buscarPorDireccion'])
        ->name('propiedades.buscarDireccion');

    // Guardar propiedad
    Route::post('/propiedades/guardar',
        [PropiedadController::class, 'guardar'])
        ->name('propiedades.guardar');

    // Favoritos (pendiente implementar)
    // Route::post('/propiedades/{propiedad}/favorito',
    //     [PropiedadController::class, 'toggleFavorito'])
    //     ->name('propiedades.favorito');

    // Notas (pendiente implementar)
    // Route::post('/propiedades/{propiedad}/nota',
    //     [PropiedadController::class, 'guardarNota'])
    //     ->name('propiedades.nota');
});

/*
|--------------------------------------------------------------------------
| Solo Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'activo', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('admin.dashboard'))
            ->name('dashboard');
    });

require __DIR__ . '/auth.php';