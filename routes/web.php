<?php

use App\Http\Controllers\AuthPortalController;
use App\Http\Controllers\PovertyPortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PovertyPortalController::class, 'home'])->name('portal.home');
Route::get('/calculadora', [PovertyPortalController::class, 'calculator'])->name('calculator.index');
Route::post('/calculadora', [PovertyPortalController::class, 'calculate'])->name('calculator.calculate');
Route::get('/analisis-temporal', [PovertyPortalController::class, 'temporal'])->name('temporal.index');
Route::get('/importar', [PovertyPortalController::class, 'importPage'])->name('import.index');
Route::post('/importar', [PovertyPortalController::class, 'processImport'])->name('import.process');
Route::get('/importar/plantilla', [PovertyPortalController::class, 'downloadTemplate'])->name('import.template');

// Autenticación Frontend y Control de Usuarios
Route::get('/login', [AuthPortalController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthPortalController::class, 'login'])->name('portal.login.submit');
Route::get('/registro', [AuthPortalController::class, 'showRegister'])->name('portal.register');
Route::post('/registro', [AuthPortalController::class, 'register'])->name('portal.register.submit');
Route::post('/logout', [AuthPortalController::class, 'logout'])->name('portal.logout');

Route::middleware('auth')->group(function () {
    Route::get('/mi-historial', [AuthPortalController::class, 'myHistory'])->name('portal.my-history');
});
