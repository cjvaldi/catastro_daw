<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropiedadController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


//   **************************************** 
// Grupo autenticado
Route::middleware(['auth'])->group(function () {

    // Panel administrador
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });

    // Propiedades (visualización)
    Route::resource('propiedades', PropiedadController::class)
        ->parameters(['propiedades' => 'propiedad'])
        ->only(['index', 'show']);

    // Búsqueda por referencia
    Route::post(
        '/propiedades/buscar',
        [PropiedadController::class, 'buscar']
    )->name('propiedades.buscar');

    // Test API
    // Referencia 2749704YJ0624N0001DI
    Route::post(
        '/propiedades/test-api',
        [PropiedadController::class, 'testApi']
    )->name('propiedades.testApi');

    // Guardar propiedad (solo admin y registrado)
    Route::post(
        '/propiedades/guardar',
        [PropiedadController::class, 'guardar']
    )->middleware('role:admin,registrado')
        ->name('propiedades.guardar');

    // Prueba para ver si se actualiza la router:list
    Route::get('/propiedades/test-get', function () {
        return 'Funciona';
    });
});
//   **************************************** 

  // Prueba para ver si se actualiza la router:list
    Route::get('/propiedades/test1', function () {
        return 'test fuera de auth';
    });

// Visitante solo puede visualizar
Route::get('/propiedades', [PropiedadController::class, 'index'])
    ->middleware(['auth'])
    ->name('propiedades.index');

require __DIR__ . '/auth.php';



// Para listar las funciones y detalles

// php artisan router:list