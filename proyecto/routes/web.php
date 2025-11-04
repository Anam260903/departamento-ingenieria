<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\InspeccionesController;
use App\Http\Controllers\InformesController;

// Ruta para mostrar el formulario de inicio de sesión
Route::get('/login', [AuthController::class, 'showLoginForm'])->name ('login');

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

// Ruta para el módulo de inspecciones (protegidas por middleware)
Route::middleware('auth')->group(function () {
    Route::resource('inspecciones', InspeccionesController::class);
});

// Ruta para marcar una inspección como completada
Route::patch('inspecciones/{id_insp}/complete', [InspeccionesController::class, 'completeInspection'])->name('inspecciones.complete');

// Ruta para exportar listado de inspecciones a PDF
Route::get('inspecciones/exportar/pdf', [InspeccionesController::class, 'exportarPDF'])->name('inspecciones.exportar.pdf');

// Ruta para exportar listado de inspecciones a PDF filtrado por mes
Route::get('inspecciones/exportar/mes', [InspeccionesController::class, 'exportarPDFMes'])->name('inspecciones.exportar.mes');

// Ruta para el módulo de informes técnicos
Route::get('informes', [InformesController::class, 'index'])->name('informes.index');

// Ruta para manejar la selección de la inspección
Route::get('informes/seleccionar', [InformesController::class, 'seleccionarInspeccion'])->name('informes.seleccionar');

// Ruta para mostrar el primer paso del informe  (Datos Generales, recibirá el id de inspección)
Route::get('informes/crear/{id_insp}', [InformesController::class, 'create'])->name('informes.create');

// Ruta para guardar el primer paso del informe (Datos Generales)
Route::post('informes/guardar-paso-1', [InformesController::class, 'storeStep1'])->name('informes.store.step1');

// Ruta para editar el primer paso del informe (Datos Generales)
Route::get('informes/editar/{id_inf}/paso-1', [InformesController::class, 'editStep1'])->name('informes.edit.step1');

// Ruta para actualizar/editar el primer paso del informe (cuando el informe ya existe)
// Usamos PUT/PATCH y requerimos el ID del informe ($id_inf)
Route::put('informes/actualizar/{id_inf}/paso-1', [InformesController::class, 'updateStep1'])->name('informes.update.step1');

// Ruta para mostrar el segundo paso del informe (Diagnóstico y observaciones)
Route::get('informes/editar/{id_inf}/paso-2', [InformesController::class, 'editStep2'])->name('informes.edit.step2');

// Ruta para guardar/actualizar el segundo paso del informe (Diagnóstico y observaciones)
Route::put('informes/actualizar/{id_inf}/paso-2', [InformesController::class, 'updateStep2'])->name('informes.update.step2');

// Ruta para mostrar el tercer paso del informe (Recomendciones y mapa)
Route::get('informes/editar/{id_inf}/paso-3', [InformesController::class, 'editStep3'])->name('informes.edit.step3');

//Ruta para guardar/actualizar el tercer paso del informe (Recomendaciones y mapa)
Route::put('informes/actualizar/{id_inf}/paso-3', [InformesController::class, 'updateStep3'])->name('informes.update.step3');