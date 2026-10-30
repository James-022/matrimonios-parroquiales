<?php

use App\Http\Controllers\AdminRolController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\ExpedienteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
        // Perfil del usuario
    Route::get('/profile', [
        ProfileController::class,
        'edit',
    ])->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class,
        'update',
    ])->name('profile.update');

    Route::delete('/profile', [
        ProfileController::class,
        'destroy',
    ])->name('profile.destroy');

    Route::view('/dashboard', 'dashboard')
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Administración de usuarios
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:Administrador')
        ->group(function () {

            Route::get('/usuarios', [
                AdminRolController::class,
                'index',
            ])->name('roles.index');

            Route::post('/usuarios', [
                AdminRolController::class,
                'store',
            ])->name('roles.store');

            Route::put('/usuarios/{user}', [
                AdminRolController::class,
                'update',
            ])->name('roles.update');
        });

    /*
    |--------------------------------------------------------------------------
    | Expedientes matrimoniales
    |--------------------------------------------------------------------------
    */

    Route::prefix('expedientes')
        ->name('expedientes.')
        ->middleware('role:Administrador,Encargado')
        ->group(function () {

            Route::get('/', [
                ExpedienteController::class,
                'index',
            ])->name('index');

            Route::get('/crear', [
                ExpedienteController::class,
                'create',
            ])->name('create');

            Route::post('/', [
                ExpedienteController::class,
                'store',
            ])->name('store');
        });

    /*
    |--------------------------------------------------------------------------
    | Revisión de documentos
    |--------------------------------------------------------------------------
    */

    Route::prefix('documentos')
        ->name('documentos.')
        ->middleware('role:Administrador,Encargado')
        ->group(function () {

            Route::get('/', [
                DocumentoController::class,
                'index',
            ])->name('index');

            Route::put('/{documento}', [
                DocumentoController::class,
                'update',
            ])->name('update');
        });
});

require __DIR__.'/auth.php';