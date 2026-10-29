<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; 
use App\Http\Controllers\RolController;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Auth::routes();

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

/*
// --- RUTAS POR DEFECTO LARAVEL ---

Route::get('roles/listar', [RolController::class, 'listar'])
    ->name('roles.listar')
    ->middleware('auth');

// 2. Rutas del CRUD (index, store, update, destroy)
Route::resource('roles', RolController::class)
    ->middleware('auth');
*/

// --- RUTAS MANUALES PARA ROLES ---
Route::middleware('auth')->group(function () {
    //Ruta para la vista (GET)
    Route::get('roles', [RolController::class, 'index'])->name('roles.index');
    
    //Ruta para DataTables (GET)
    Route::get('roles/listar', [RolController::class, 'listar'])->name('roles.listar');
    
    //Ruta para guardar (POST)
    Route::post('roles', [RolController::class, 'guardar'])->name('roles.guardar');
    
    //Ruta para eliminar (DELETE)
    Route::delete('roles/{id}', [RolController::class, 'eliminar'])->name('roles.eliminar');

    //Ruta para obtener los datos de un rol específico (GET)
    Route::get('roles/{id}', [RolController::class, 'mostrar'])->name('roles.mostrar');
    
    //Ruta para actualizar el rol (PUT)
    Route::put('roles/{id}', [RolController::class, 'editar'])->name('roles.editar');

 // --- RUTAS MANUALES PARA PERMISOS ---   
    //Ruta para listar permisos (GET)-Evitando crear el controlador de permisos por ahora
    Route::get('permisos/listar', function() {
    return response()->json(App\Models\Permiso::all());
    })->middleware('auth');
});    