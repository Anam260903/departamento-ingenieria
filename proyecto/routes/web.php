<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;

Route::get('/', function () {
    return view('welcome');
});
route::get('/ejemplo', function () {
    return view('ejemplo');
});

// Ruta para mostrar el formulario de inicio de sesión
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Ruta para procesar la solicitud de inicio de sesión
Route::post('/login', [AuthController::class, 'login']);

// Rutas de recuperación de contraseña
Route::get('forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

// Ruta para mostrar el formulario de registro
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');

// Ruta para procesar la solicitud de registro
Route::post('/register', [AuthController::class, 'register']);