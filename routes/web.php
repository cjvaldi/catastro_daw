<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropiedadController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UpgradeController;

/*
|--------------------------------------------------------------------------
| Rutas públicas (Anónimo permitido)
| Usuario Anónimo solo puede buscar por referencia, la función buscar es pública
| Anónimo, visitante, registrado, admin|
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::post('/propiedades/buscar', [PropiedadController::class, 'buscar'])
    ->name('propiedades.buscar');

Route::get('/propiedades', [PropiedadController::class, 'index'])
    ->name('propiedades.index');
// Test API 
// Referencia 2749704YJ0624N0001DI 
Route::post('/propiedades/test-api', [PropiedadController::class, 'testApi'])
    ->name('propiedades.testApi');

/*
|--------------------------------------------------------------------------
| Visitante + Registrado + Admin — Requiere login
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','activo'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['verified'])->name('dashboard');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ver detalle + historial — Visitante
    Route::get('/propiedades/{propiedad}', [PropiedadController::class, 'show'])
        ->name('propiedades.show');

    Route::get('/historial', [PropiedadController::class, 'historial'])
        ->name('propiedades.historial');

    // Solicitar upgrade a Premium
    Route::get('/upgrade', [ProfileController::class, 'showUpgrade'])
        ->name('profile.upgrade');
    Route::post('/upgrade', [ProfileController::class, 'requestUpgrade'])
        ->name('profile.upgrade.request');

    // Upgrade Premium — solo visitantes
Route::get('/upgrade', [UpgradeController::class, 'show'])
    ->name('upgrade.show');
Route::post('/upgrade', [UpgradeController::class, 'upgrade'])
    ->name('upgrade.process');    
});

/*
|--------------------------------------------------------------------------
| Solo Registrado (Premium) + Admin — Escritura en BD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','activo','role:registrado,admin'])->group(function () {

    Route::post('/propiedades/guardar', [PropiedadController::class, 'guardar'])
        ->name('propiedades.guardar');

    Route::post('/propiedades/{propiedad}/favorito', [PropiedadController::class, 'toggleFavorito'])
        ->name('propiedades.favorito');

    Route::post('/propiedades/{propiedad}/nota', [PropiedadController::class, 'guardarNota'])
        ->name('propiedades.nota');

    Route::delete('/propiedades/{propiedad}/nota/{nota}', [PropiedadController::class, 'eliminarNota'])
        ->name('propiedades.nota.eliminar');
});

/*
|--------------------------------------------------------------------------
| Solo Admin — Panel completo
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'activo','role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/dashboard', fn() => view('admin.dashboard'))
        ->name('dashboard');

    Route::get('/usuarios', [\App\Http\Controllers\Admin\UsuarioController::class, 'index'])
        ->name('usuarios.index');

    Route::patch('/usuarios/{user}/rol', [\App\Http\Controllers\Admin\UsuarioController::class, 'updateRol'])
        ->name('usuarios.rol');

    Route::patch('/usuarios/{user}/toggle', [\App\Http\Controllers\Admin\UsuarioController::class, 'toggle'])
        ->name('usuarios.toggle');

    Route::get('/logs', [\App\Http\Controllers\Admin\LogController::class, 'index'])
        ->name('logs.index');
});

require __DIR__ . '/auth.php';

// Para listar las funciones y detalles
// php artisan router:list