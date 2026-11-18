<?php

use Illuminate\Support\Facades\Route;
use Orlando\VoterLoyalty\Http\Controllers\VoterController;
use Orlando\VoterLoyalty\Http\Controllers\UserController;

// Filtro global bajo el middleware 'web' para el ciclo de sesiones
Route::group(['middleware' => ['web']], function () {

    // Rutas de autenticación nativas
    Route::get('/', [VoterController::class, 'showLoginForm'])->name('login');
    Route::get('login', [VoterController::class, 'showLoginForm']);
    Route::post('login', [VoterController::class, 'login'])->name('login.post');
    Route::post('logout', [VoterController::class, 'logout'])->name('logout');

    // Rutas del panel de control de FidelizaCRM Engine (Votantes)
    Route::get('voters/check-cedula', [VoterController::class, 'checkCedula'])->name('voters.checkCedula');
    Route::put('voters/{id}/clear-note', [VoterController::class, 'clearNote'])->name('voters.clearNote');
    Route::get('voters', [VoterController::class, 'index'])->name('voters.index');
    Route::post('voters', [VoterController::class, 'store'])->name('voters.store');
    Route::put('voters/{id}', [VoterController::class, 'update'])->name('voters.update');
    Route::delete('voters/{id}', [VoterController::class, 'destroy'])->name('voters.destroy');
    Route::post('voters/bulk-delete', [VoterController::class, 'bulkDestroy'])->name('voters.bulkDestroy');

    // 🛡️ CONTROL DE SEGURIDAD PARA GESTIÓN DE USUARIOS
    // Agregamos el middleware 'auth' para proteger el módulo a nivel de rutas
    Route::group(['middleware' => ['auth']], function () {

        // Rutas de la pantalla web para administrar usuarios
        Route::get('users', [UserController::class, 'index'])->name('users.index');        // Listar
        Route::post('users', [UserController::class, 'store'])->name('users.store');       // Crear
        Route::put('users/{id}', [UserController::class, 'update'])->name('users.update'); // Cambiar rol/Editar
        Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('voters/bulk-reassign', [VoterController::class, 'bulkReassign'])->name('voters.bulkReassign');
    });
});
