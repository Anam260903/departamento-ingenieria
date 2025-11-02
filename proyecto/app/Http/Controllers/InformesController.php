<?php

namespace App\Http\Controllers;
use App\Models\Informe;
use App\Models\Inspeccion;
use App\Models\Propietario;
use App\Models\Vivienda;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InformesController extends Controller
{
    /**
     * Mostrar el listado de informes técnicos.
     */
    public function index()
    {
        // 1. Obtener los informes
        $informes = Informe::with([
            'inspeccion.vivienda.propietario',
            'inspeccion.usuario'
        ])
            ->orderBy('fecha_inf', 'desc')
            ->paginate(10); // Paginación

        return view('informes-tecnicos.index', compact('informes'));
    }

    /**
     * Devuelve una lista de inspecciones completadas que aún no tienen un informe.
     * Esto se usa para poblar el modal de selección.
     */
    public function obtenerInspeccionesDisponibles()
    {
        // 1. Obtener los IDs de las inspecciones que ya tienen un informe
        $inspeccionesConInforme = Informe::pluck('id_insp');

        // 2. Obtener las inspecciones que están 'Completadas' y no tienen un informe
        $inspecciones = Inspeccion::where('estado_insp', 1)
            ->whereNotIn('id_insp', $inspeccionesConInforme)
            ->with('vivienda.propietario')
            ->get();

        return $inspecciones;
    }

    /**
     * Muestra el formulario de creación del Informe Técnico.
     * @param int $id_insp
     */
    public function create($id_insp)
    {
        // 1. Cargar la inspección y sus relaciones para prellenar el formulario
        $inspeccion = Inspeccion::with([
            'vivienda.propietario',
            'usuario'
        ])->findOrFail($id_insp);

        // 2. Prepara la fecha del informe con la fecha de la inspección
        $fecha_inf = Carbon::parse($inspeccion->fecha_insp)->format('Y-m-d');

        // El paso actual es 1 (Datos Generales)
        $pasoActual = 1;

        // 3. Pasar los datos a la vista
        return view('informes-tecnicos.create', compact('inspeccion', 'fecha_inf', 'pasoActual'));
    }

    public function storeStep1(Request $request)
    {
        // 1. Validación de los datos del Paso 1
        $validatedData = $request->validate([
            'id_insp' => [
                'required',
                'integer',
                // Asegurar que la inspección exista
                Rule::exists('inspecciones', 'id_insp'),
                // Asegurar que NO haya ya un informe asociado (Creación inicial)
                Rule::unique('informes', 'id_insp')
            ],
            // Datos del Informe (fecha_inf y comunidad)
            'fecha_inf' => 'required|date',
            'comunidad' => 'required|string|max:50',

            // Datos del Propietario (Responsable) - Se actualizarán si cambian
            'propietario_cedula' => 'required|string|max:8|regex:/^[0-9]+$/',
            'propietario_nombre' => 'required|string|max:30|regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/',
            'propietario_apellido' => 'required|string|max:30|regex:/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/',
            'propietario_telefono' => 'required|string|max:11|regex:/^[0-9]+$/',

            // Datos de la Vivienda (Dirección) - Se actualizarán si cambian
            'direccion' => 'required|string|max:100',
        ]);

        $informe = null;

        try {
            // Usamos una transacción para asegurar que todas las operaciones se completen
            $informe = DB::transaction(function () use ($validatedData) {

                // 2. Obtener la Inspección y sus relaciones para obtener IDs
                $inspeccion = Inspeccion::with('vivienda.propietario')->findOrFail($validatedData['id_insp']);
                $vivienda = $inspeccion->vivienda;
                $propietario = $vivienda->propietario;

                // 3. Actualizar datos del Propietario (Responsable)
                $propietario->update([
                    'cedula_propie' => $validatedData['propietario_cedula'],
                    'nombre_propie' => $validatedData['propietario_nombre'],
                    'apellido_propie' => $validatedData['propietario_apellido'],
                    'telefono' => $validatedData['propietario_telefono'],
                ]);

                // 4. Actualizar datos de la Vivienda (Dirección)
                $vivienda->update([
                    'direccion' => $validatedData['direccion'],
                ]);

                // 5. Crear el registro inicial del informe
                return Informe::create([
                    'id_insp' => $validatedData['id_insp'],
                    'fecha_inf' => $validatedData['fecha_inf'],
                    'comunidad' => $validatedData['comunidad'],
                    // Los demás campos (antecedentes, planteamiento, etc.) se quedan en NULL
                ]);
            });

            // 6. Redirigir al siguiente paso
            if ($informe) {

                $redirectUrl = route('informes.edit.step2', ['id_inf' => $informe->id_inf]);

                // ❌ LÍNEA TEMPORAL DE DEBUGGING (descomenta esto para ver la URL generada) ❌
                //dd("Redireccionando a:", $redirectUrl); 

                return redirect($redirectUrl) // Usamos redirect() directo en lugar de route() para mayor certeza
                    ->with('success', 'Paso 1: Datos generales guardados. Continúe con el paso 2.');
            }

        } catch (\Exception $e) {
            // ❌ ¡CÓDIGO TEMPORAL DE DEBUGGING! ❌
            // Descomenta la siguiente línea para ver el error exacto y luego elimínala.
            //dd($e->getMessage(), $e->getFile(), $e->getLine());

            return back()->withInput()->with('error', 'Error de Transacción. Detalles: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para el Paso 2 de informe técnico: Diagnóstico y observaciones.
     */
    public function editStep2($id_inf)
    {
        // 1. Buscar el informe existente y cargar las relaciones necesarias:
        $informe = Informe::with('inspeccion.vivienda')->findOrFail($id_inf);

        // La vista accede a $informe->inspeccion->vivienda->caracteristicas
        return view('informes-tecnicos.edit-step2', compact('informe'));
    }

    /**
     * Guardar y actualizar los datos del Paso 2 (columna 'antecedentes').
     */
    public function updateStep2(Request $request, $id_inf)
    {
        // 1. Validación de los cuatro campos
        $validatedData = $request->validate([
            'antecedentes' => 'required|string',
            'planteamiento' => 'required|string',
            'resultados' => 'required|string',
            'caracteristicas' => 'required|string',
        ]);

        try {
            // 2. Buscar el informe existente y sus relaciones
            $informe = Informe::with('inspeccion.vivienda')->findOrFail($id_inf);
            $vivienda = $informe->inspeccion->vivienda;

            DB::transaction(function () use ($informe, $vivienda, $validatedData) {

                // A. Actualizar campos de la tabla informes
                $informe->update([
                    'antecedentes' => $validatedData['antecedentes'],
                    'planteamiento' => $validatedData['planteamiento'],
                    'resultados' => $validatedData['resultados'],
                ]);

                // B. Actualizar el campo 'caracteristicas' en la tabla viviendas
                $vivienda->update([
                    'caracteristicas' => $validatedData['caracteristicas'],
                ]);

            });

            // 3. Redirigir al siguiente paso
            return redirect()->route('informes.edit.step3', ['id_inf' => $informe->id_inf])
                ->with('success', 'Paso 2: Diagnóstico y observaciones guardados correctamente. Continúe con el paso 3.');

        } catch (\Exception $e) {
            // En caso de error (ej. Mass Assignment, fallo de DB, etc.)
            return back()->withInput()->with('error', 'Error al guardar los datos del Paso 2: ' . $e->getMessage());
        }
    }

    /**
     * Placeholder para el siguiente paso (Paso 3: Recomendaciones).
     */
    public function editStep3($id_inf)
    {
        // TO DO: Implementar la lógica para cargar el Paso 3
        return view('informes-tecnicos.edit-step3', compact('id_inf'))->with('info', 'El Paso 3: Recomendaciones aún no ha sido implementado.');
    }
}
