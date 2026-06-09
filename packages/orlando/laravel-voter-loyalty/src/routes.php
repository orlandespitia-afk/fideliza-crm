<?php

use Illuminate\Support\Facades\Route;
use Orlando\VoterLoyalty\Http\Controllers\VoterController;


// Filtro global bajo el middleware 'web' para el ciclo de sesiones
Route::group(['middleware' => ['web']], function () {

    // Rutas de autenticación nativas
    Route::get('/', [VoterController::class, 'showLoginForm'])->name('login');
    Route::get('login', [VoterController::class, 'showLoginForm']);
    Route::post('login', [VoterController::class, 'login'])->name('login.post');
    Route::post('logout', [VoterController::class, 'logout'])->name('logout');

    // Rutas del panel de control de FidelizaCRM Engine
    Route::get('voters', [VoterController::class, 'index'])->name('voters.index');
    Route::post('voters', [VoterController::class, 'store'])->name('voters.store');
    Route::put('voters/{id}', [VoterController::class, 'update'])->name('voters.update');

    // Rutas del panel de control de FidelizaCRM Engine
    Route::get('voters', [VoterController::class, 'index'])->name('voters.index');
    Route::post('voters', [VoterController::class, 'store'])->name('voters.store');
    Route::put('voters/{id}', [VoterController::class, 'update'])->name('voters.update');

    // 🟢 NUEVAS RUTAS DE ELIMINACIÓN (Protegidas)
    Route::delete('voters/{id}', [VoterController::class, 'destroy'])->name('voters.destroy');
    Route::post('voters/bulk-delete', [VoterController::class, 'bulkDestroy'])->name('voters.bulkDestroy');
});
