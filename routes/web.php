<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropiedadController;
use App\Http\Controllers\UpgradeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

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

// Búsqueda pública por referencia
Route::post('/propiedades/buscar', [PropiedadController::class, 'buscar'])
    ->name('propiedades.buscar');

/*
|--------------------------------------------------------------------------
| Visitante + Registrado + Admin — Requiere login
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'activo'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');

    // Ver mis propiedades guardadas
    Route::get('/propiedades', [PropiedadController::class, 'index'])
        ->name('propiedades.index');

    // Ver detalle de propiedad
    Route::get('/propiedades/{propiedad}', [PropiedadController::class, 'show'])
        ->name('propiedades.show');

    // Historial
    Route::get('/historial', [PropiedadController::class, 'historial'])
        ->name('propiedades.historial');

    // Guardar propiedad
    Route::post('/propiedades/guardar', [PropiedadController::class, 'guardar'])
        ->name('propiedades.guardar');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // Upgrade a Premium
    Route::get('/upgrade', [UpgradeController::class, 'show'])
        ->name('upgrade.show');
    Route::post('/upgrade', [UpgradeController::class, 'upgrade'])
        ->name('upgrade.process');
});

/*
|--------------------------------------------------------------------------
| Solo Registrado (Premium) + Admin — Favoritos y Notas
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'activo', 'role:registrado,admin'])->group(function () {

    // Búsqueda por dirección
    Route::get('/propiedades/buscar-direccion', function () {
        return view('propiedades.buscar-direccion');
    })->name('propiedades.formBuscarDireccion');

    Route::post('/propiedades/buscar-direccion', [PropiedadController::class, 'buscarPorDireccion'])
        ->name('propiedades.buscarDireccion');

    // Favoritos
    Route::post('/propiedades/{propiedad}/favorito', [PropiedadController::class, 'toggleFavorito'])
        ->name('propiedades.favorito');

    // Notas
    Route::post('/propiedades/{propiedad}/nota', [PropiedadController::class, 'guardarNota'])
        ->name('propiedades.nota');

    Route::delete('/propiedades/{propiedad}/nota/{nota}', [PropiedadController::class, 'eliminarNota'])
        ->name('propiedades.nota.eliminar');
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
        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/usuarios', [AdminController::class, 'usuarios'])
            ->name('usuarios');

        Route::post('/usuarios/{user}/rol', [AdminController::class, 'cambiarRol'])
            ->name('usuarios.rol');

        Route::post('/usuarios/{user}/toggle', [AdminController::class, 'toggleActivo'])
            ->name('usuarios.toggle');

        Route::get('/logs', [AdminController::class, 'logs'])
            ->name('logs');
    });

require __DIR__ . '/auth.php';