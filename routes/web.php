<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropiedadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


//   ************* MIS RUTAS *************************** 
/*
|--------------------------------------------------------------------------
| Rutas públicas (Anónimo permitido)
| Usuario Anónimo solo puede buscar por referencia, la función buscar es pública
| Anónimo, visitante, registrado, admin|
|--------------------------------------------------------------------------
*/
Route::post('/propiedades/buscar', [PropiedadController::class, 'buscar'])
    ->name('propiedades.buscar');

// Test API 
// Referencia 2749704YJ0624N0001DI 
Route::post('/propiedades/test-api', [PropiedadController::class, 'testApi'])
    ->name('propiedades.testApi');



/*
|--------------------------------------------------------------------------
| Rutas autenticadas (Visitante + Registrado + Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['verified'])->name('dashboard');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Listado propiedades (solo autenticados)
    Route::get('/propiedades', [PropiedadController::class, 'index'])
        ->name('propiedades.index');

    Route::get('/propiedades/{propiedad}', [PropiedadController::class, 'show'])
        ->name('propiedades.show');
});

/*
|--------------------------------------------------------------------------
| Solo Registrado y Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','role:admin,registrado'])->group(function () {
    Route::post('/propiedades/guardar', [PropiedadController::class, 'guardar'])
        ->name('propiedades.guardar');
});

/*
|--------------------------------------------------------------------------
| Solo Admin
|--------------------------------------------------------------------------
*/
Route::middleware('auth', 'role:admin')->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

require __DIR__ . '/auth.php';

// Para listar las funciones y detalles
// php artisan router:list