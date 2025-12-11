<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\PreguntasSeguridadController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\RecursosController;
use App\Http\Controllers\InspeccionesController;
use App\Http\Controllers\InformesController;
use App\Http\Controllers\CalculosController;
use App\Http\Controllers\DecisionesController;
use App\Http\Controllers\NotificacionController;

// Ruta para mostrar el formulario de inicio de sesión
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Ruta para procesar la solicitud de inicio de sesión
Route::post('/login', [AuthController::class, 'login']);

// Ruta para extender la sesión vía AJAX
Route::post('/session/extend', function (Illuminate\Http\Request $request) {
    // Solo accedemos a una variable de sesión para actualizar el tiempo de actividad
    $request->session()->put('active_check', time());
    return response()->json(['status' => 'extended']);
})->name('session.extend')->middleware('auth'); // Asegúrate que solo sea accesible si está logueado

// Rutas para registrar preguntas de seguridad después dellogin por primera vez
Route::get('/registrar/preguntas-seguridad', [PreguntasSeguridadController::class, 'showRegistrationForm'])->name('form.preguntasSeguridad')->middleware('auth');
Route::post('/registar/preguntas-seguridad', [PreguntasSeguridadController::class, 'registerQuestions'])->name('register.preguntasSeguridad')->middleware('auth');

// Rutas para recuperar contraseña

// Paso 1: Pedir el correo/cédula para identificar al usuario
Route::get('/has-olvidado-tu-contraseña', [PreguntasSeguridadController::class, 'showIdentifierForm'])->name('form.olvideContraseña');
Route::post('/has-olvidado-tu-contraseña/identificar', [PreguntasSeguridadController::class, 'identifyUser'])->name('identify.olvideContraeña');

// Paso 2: Mostrar pregunta de seguridad aleatoria y pedir respuesta
Route::get('/responder/preguntas-seguridad', [PreguntasSeguridadController::class, 'showChallengeForm'])->name('form.preguntas')->middleware('guest');
Route::post('/responder/preguntas-seguridad', [PreguntasSeguridadController::class, 'validateAnswer'])->name('validate.preguntas');

// Paso 3: Restablecer la contraseña (si la respuesta fue correcta)
Route::get('/restablecer-contraseña-de-seguridad', [PreguntasSeguridadController::class, 'showResetForm'])->name('form.reestablecerContraseña')->middleware('guest');
Route::post('restablecer-contraseña', [PreguntasSeguridadController::class, 'resetPassword'])->name('update.contraseña');

// Ruta para mostrar el formulario de registro
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');

// Ruta para procesar la solicitud de registro
Route::post('/register', [AuthController::class, 'register']);

//Ruta para cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ruta del dashboard (protegida por middleware)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Ruta para mostrar el perfil (GET, protegida por middleware)
Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil')->middleware('auth');

// Ruta para actualizar los datos del perfil (POST, protegida por middleware)
Route::post('/perfil/actualizar', [PerfilController::class, 'update'])->name('perfil.update')->middleware('auth');

// Ruta para actualizar la contraseña del perfil (POST, protegida por middleware)
Route::post('/perfil/cambiar-contrasena', [PerfilController::class, 'changePassword'])->name('perfil.change-password')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    
    // --- Rutas de Actualización de Preguntas de Seguridad ---
    // 1. Verifica la contraseña actual
    Route::post('/perfil/seguridad/verificar-password', [PreguntasSeguridadController::class, 'verifyCurrentPassword'])->name('seguridad.verificarContraseña');
    
    // 2. Muestra el formulario para registrar las nuevas preguntas
    Route::get('/perfil/seguridad/actualizar', [PreguntasSeguridadController::class, 'showUpdateForm'])->name('seguridad.form.actualizarPreguntas');
    
    // 3. Procesa la actualización de las preguntas
    Route::post('/perfil/seguridad/guardar', [PreguntasSeguridadController::class, 'updateQuestions'])->name('seguridad.actualizarPreguntas');
});

// Ruta para mostrar la lista del personal
Route::get('personal', [PersonalController::class, 'index'])->name('personal.index')->middleware('auth');

// Ruta para mostrar el formulario de edición de personal
Route::get('/personal/{personal}/editar', [PersonalController::class, 'edit'])->name('personal.edit')->middleware('auth');

// Ruta para actualizar los datos del personal
Route::put('/personal/{personal}', [PersonalController::class, 'update'])->name('personal.update')->middleware('auth');

// Ruta para cambiar el estado (Activo/Inactivo) del personal
Route::patch('/personal/{personal}/toggle-status', [PersonalController::class, 'toggleStatus'])->name('personal.toggleStatus')->middleware('auth');

// Ruta para mostrar la interfaz de asignación de inspecciones al personal
// 1. Ruta GET para que JavaScript obtenga las inspecciones disponibles (vía AJAX).
Route::get('personal/{personal}/get-inspecciones', [PersonalController::class, 'getAvailableInspections'])->name('personal.getAvailableInspections')->middleware('auth');

// 2. Ruta POST para guardar la asignación de la inspección al usuario.
Route::post('personal/{personal}/asignar-inspeccion', [PersonalController::class, 'assignInspection'])->name('personal.assignInspection')->middleware('auth');

// Ruta para mostrar la interfaz de asignación de recursos al personal
Route::get('/personal/get-recursos-disponibles', [PersonalController::class, 'getRecursosDisponibles'])->name('personal.getRecursosDisponibles')->middleware('auth');

// Ruta para guardar la asignación del recurso al usuario
Route::post('/personal/{id_user}/asignar-recurso', [PersonalController::class, 'assignRecurso'])->name('personal.assignRecurso')->middleware('auth');

// Ruta para exportar listado de personal a PDF
Route::get('personal/exportar/pdf', [PersonalController::class, 'exportarPersonalPDF'])->name('personal.exportar.pdf')->middleware('auth');

// Ruta para exportar listado de personal a PDF filtrado por profesión
Route::get('personal/exportar/pdf/{profesion}', [PersonalController::class, 'exportarPersonalPorProfesionPDF'])->name('personal.exportar.pdf.filtro')->middleware('auth');

// Rutas del módulo de recursos protegidas por middleware
Route::group(['middleware' => ['auth']], function () {

    // Rutas Resource (cubre index, create, store, edit, update, destroy)
    Route::resource('recursos', RecursosController::class)->names('recursos');

    // Ruta específica para el historial de asignaciones
    Route::get('recursos/asignaciones/historial', [RecursosController::class, 'assignmentsHistory'])->name('recursos.assignments.history');

    // Ruta para marcar como devuelto
    Route::put('asignaciones/{id_asignacion}/return', [RecursosController::class, 'markAsReturned'])->name('recursos.assignments.mark-returned');

    // Ruta para exportar listado general de recursos a PDF
    Route::get('recursos/exportar/pdf', [RecursosController::class, 'exportarRecursosGeneralPDF'])->name('recursos.exportar.pdf.general');

    // Ruta para exportar historial de asignaciones a PDF
    Route::get('recursos/asignaciones/exportar/pdf', [RecursosController::class, 'exportarHistorialAsignacionesPDF'])->name('recursos.asignaciones.exportar.pdf');

    // Ruta para exportar historial de asignaciones filtrado por rango de fechas a PDF
    Route::get('recursos/asignaciones/exportar/pdf/fecha', [RecursosController::class, 'exportarHistorialAsignacionesPorFechaPDF'])->name('recursos.asignaciones.exportar.pdf.fecha');
});

// Ruta para el módulo de inspecciones (protegidas por middleware)
Route::middleware('auth')->group(function () {
    Route::resource('inspecciones', InspeccionesController::class);
});

// Ruta para marcar una inspección como completada
Route::patch('inspecciones/{id_insp}/complete', [InspeccionesController::class, 'completeInspection'])->name('inspecciones.complete')->middleware('auth');

// Ruta para exportar listado de inspecciones a PDF
Route::get('inspecciones/exportar/pdf', [InspeccionesController::class, 'exportarPDF'])->name('inspecciones.exportar.pdf')->middleware('auth');

// Ruta para exportar listado de inspecciones a PDF filtrado por mes
Route::get('inspecciones/exportar/mes', [InspeccionesController::class, 'exportarPDFMes'])->name('inspecciones.exportar.mes')->middleware('auth');

// Ruta para el módulo de informes técnicos
Route::get('informes', [InformesController::class, 'index'])->name('informes.index')->middleware('auth');

// Ruta para manejar la selección de la inspección
Route::get('informes/seleccionar', [InformesController::class, 'seleccionarInspeccion'])->name('informes.seleccionar')->middleware('auth');

// Ruta para mostrar el primer paso del informe  (Datos Generales, recibirá el id de inspección)
Route::get('informes/crear/{id_insp}', [InformesController::class, 'create'])->name('informes.create')->middleware('auth');

// Ruta para guardar el primer paso del informe (Datos Generales)
Route::post('informes/guardar-paso-1', [InformesController::class, 'storeStep1'])->name('informes.store.step1')->middleware('auth');

// Ruta para editar el primer paso del informe (Datos Generales)
Route::get('informes/editar/{id_inf}/paso-1', [InformesController::class, 'editStep1'])->name('informes.edit.step1')->middleware('auth');

// Ruta para actualizar/editar el primer paso del informe (cuando el informe ya existe)
// Usamos PUT/PATCH y requerimos el ID del informe ($id_inf)
Route::put('informes/actualizar/{id_inf}/paso-1', [InformesController::class, 'updateStep1'])->name('informes.update.step1')->middleware('auth');

// Ruta para mostrar el segundo paso del informe (Diagnóstico y observaciones)
Route::get('informes/editar/{id_inf}/paso-2', [InformesController::class, 'editStep2'])->name('informes.edit.step2')->middleware('auth');

// Ruta para guardar/actualizar el segundo paso del informe (Diagnóstico y observaciones)
Route::put('informes/actualizar/{id_inf}/paso-2', [InformesController::class, 'updateStep2'])->name('informes.update.step2')->middleware('auth');

// Ruta para mostrar el tercer paso del informe (Recomendciones y mapa)
Route::get('informes/editar/{id_inf}/paso-3', [InformesController::class, 'editStep3'])->name('informes.edit.step3')->middleware('auth');

//Ruta para guardar/actualizar el tercer paso del informe (Recomendaciones y mapa)
Route::put('informes/actualizar/{id_inf}/paso-3', [InformesController::class, 'updateStep3'])->name('informes.update.step3')->middleware('auth');

// Ruta para mostrar la vista del cuarto paso del informe (Materiales)
Route::get('informes/editar/{id_inf}/paso-4', [InformesController::class, 'editStep4'])->name('informes.edit.step4')->middleware('auth');

// Ruta para guardar/actualizar el cuarto paso del informe (Materiales)
Route::post('informes/actualizar/paso-4/{id_inf}', [InformesController::class, 'updateStep4'])->name('informes.update.step4')->middleware('auth');

// Ruta para mostrar la vista del quinto paso del informe (Evidencia fotográfica)
Route::get('informes/editar/{id_inf}/paso-5', [InformesController::class, 'editStep5'])->name('informes.edit.step5')->middleware('auth');

// Ruta para guardar/actualizar el quinto paso del informe (Evidencia fotográfica) y finalizar el informe
Route::post('informes/actualizar/paso-5/{id_inf}', [InformesController::class, 'updateStep5'])->name('informes.update.step5')->middleware('auth');

// Ruta para eliminar una imagen del informe técnico
Route::delete('/informes/imagen/{id_evid}', [InformesController::class, 'destroyImage'])->name('informes.delete.image')->middleware('auth');

// Ruta para eliminar un informe técnico
Route::delete('/informes/eliminar/{id_inf}', [InformesController::class, 'destroy'])->name('informes.destroy')->middleware('auth');

// Ruta para generar y descargar el PDF de un informe técnico
Route::get('informes/exportar/{id_inf}/pdf', [InformesController::class, 'downloadPdf'])->name('informes.generatePdf')->middleware('auth');

// Ruta para mostrar la estimación de materiales
Route::get('/estimacion-materiales', [CalculosController::class, 'index'])->name('estimacion-materiales')->middleware('auth');

// Rutas para el módulo de toma de decisiones
// Ruta para el index
Route::get('/toma-decisiones', [DecisionesController::class, 'index'])->name('decisiones.index')->middleware('auth');

// Ruta toma de decisiones de Recursos
Route::get('/toma-decisiones/recursos', [DecisionesController::class, 'showRecursos'])->name('decisiones.recursos')->middleware('auth');

// Endpoint AJAX para actualizar el Top N
Route::get('/decisiones/recursos/top', [DecisionesController::class, 'obtenerTopRecursosJson'])->name('decisiones.recursos.top')->middleware('auth');

// Ruta para el resumen de inspecciones
Route::get('/toma-decisiones/resumen-inspecciones', [DecisionesController::class, 'resumenInspeccion'])->name('decisiones.resumenInspeccion')->middleware('auth');

// Ruta API para el resumen del modal
Route::get('/api/inspeccion/resumen/{inspeccion}', [DecisionesController::class, 'obtenerResumenInspeccion'])->name('api.inspeccion.resumen')->middleware('auth');

// Ruta para la comparación de inspecciones
Route::get('/toma-decisiones/comparacion', [DecisionesController::class, 'comparacion'])->name('decisiones.comparacion')->middleware('auth');

// Rutas para notificaciones
Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notifications.index');
Route::get('/notificaciones/{notificacion}/leida', [NotificacionController::class, 'markAsRead'])->name('notifications.markAsRead');
Route::post('/notificaciones/marcar-todo-leido', [NotificacionController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');