<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RolController;
use App\Http\Controllers\EmpleadoController; 

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Auth::routes();

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// ==========================================
// RUTAS PROTEGIDAS 
// ==========================================
Route::middleware('auth')->group(function () {
    
    // --- RUTAS PARA ROLES ---
    Route::get('roles', [RolController::class, 'index'])->name('roles.index');
    Route::get('roles/listar', [RolController::class, 'listar'])->name('roles.listar');
    Route::post('roles', [RolController::class, 'guardar'])->name('roles.guardar');
    Route::get('roles/{id}', [RolController::class, 'mostrar'])->name('roles.mostrar');
    Route::put('roles/{id}', [RolController::class, 'editar'])->name('roles.editar');
    Route::delete('roles/{id}', [RolController::class, 'eliminar'])->name('roles.eliminar');

    // --- RUTAS PARA PERMISOS ---
    Route::get('permisos/listar', function() {
        return response()->json(App\Models\Permiso::all());
    })->name('permisos.listar');

    // --- RUTAS PARA EMPLEADOS ---
    Route::get('empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::get('empleados/listar', [EmpleadoController::class, 'listar'])->name('empleados.listar');
    Route::post('empleados', [EmpleadoController::class, 'guardar'])->name('empleados.guardar');
    Route::get('empleados/{id}', [EmpleadoController::class, 'mostrar'])->name('empleados.mostrar');
    Route::put('empleados/{id}', [EmpleadoController::class, 'editar'])->name('empleados.editar');
    Route::delete('empleados/{id}', [EmpleadoController::class, 'eliminar'])->name('empleados.eliminar');

    // --- RUTAS MANUALES PARA USUARIOS ---
    Route::get('usuarios', [App\Http\Controllers\UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('usuarios/listar', [App\Http\Controllers\UsuarioController::class, 'listar'])->name('usuarios.listar');
    // Ruta para generar el nombre y obtener los select
    Route::get('usuarios/datos-formulario', [App\Http\Controllers\UsuarioController::class, 'datosFormulario'])->name('usuarios.datos');
    Route::post('usuarios', [App\Http\Controllers\UsuarioController::class, 'guardar'])->name('usuarios.guardar');
    Route::get('usuarios/{id}', [App\Http\Controllers\UsuarioController::class, 'mostrar'])->name('usuarios.mostrar');
    Route::put('usuarios/{id}', [App\Http\Controllers\UsuarioController::class, 'editar'])->name('usuarios.editar');
    //Route::delete('usuarios/{id}', [App\Http\Controllers\UsuarioController::class, 'eliminar'])->name('usuarios.eliminar');
    Route::patch('usuarios/{id}/estado', [App\Http\Controllers\UsuarioController::class, 'cambiarEstado'])->name('usuarios.estado');
});