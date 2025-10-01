<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\InspeccionesController;

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

//Ruta para cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ruta del dashboard (protegida por middleware)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

// Ruta para mostrar el perfil (GET, protegida por middleware)
Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil')->middleware('auth');

// Ruta para actualizar los datos del perfil (POST, protegida por middleware)
Route::post('/perfil/actualizar', [PerfilController::class, 'update'])->name('perfil.update')->middleware('auth');

// Ruta para actualizar la contraseña del perfil (POST, protegida por middleware)
Route::post('/perfil/cambiar-contrasena', [PerfilController::class, 'changePassword'])->name('perfil.change-password')->middleware('auth');

// Rutas de Inspecciones (protegidas por middleware)
Route::middleware('auth')->group(function () {
    Route::resource('inspecciones', InspeccionesController::class);
});

// Ruta para marcar una inspección como completada
Route::patch('inspecciones/{id_insp}/complete', [InspeccionesController::class, 'completeInspection'])->name('inspecciones.complete');