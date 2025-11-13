<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calculos;

class CalculosController extends Controller
{
    /**
     * Recupera el contenido de uno o más cálculos por sus IDs.
     */
    public function getContenido(Request $request)
    {
        // El frontend envía los IDs en el cuerpo de la petición POST
        $calculoIds = $request->input('calculos_ids', []);

        // 1. Validar que los IDs sean un array y contengan valores
        if (!is_array($calculoIds) || empty($calculoIds)) {
            // Devolvemos una respuesta de error 400 (Bad Request)
            return response()->json([
                'error' => 'Se requieren IDs de cálculos válidos.'
            ], 400);
        }

        try {
            // 2. Consultar la base de datos
            // Buscamos solo los campos 'id_calculo' y el 'contenido'
            // donde el 'id_calculo' esté dentro del array de IDs recibidos.
            $calculos = calculos::whereIn('id_calculo', $calculoIds)
                                 ->select('id_calculo', 'contenido')
                                 ->get();
            
            // 3. Extraer solo el contenido y convertir la colección en un array simple
            // El método 'pluck' extrae los valores de un campo específico.
            $contenidosArray = $calculos->pluck('contenido')->toArray();
            
            // 4. Devolver la respuesta en el formato JSON esperado por el frontend
            // El frontend espera la clave 'contenido' que es un ARRAY de strings.
            return response()->json([
                'contenido' => $contenidosArray
            ]);

        } catch (\Exception $e) {
            
            // Devolvemos una respuesta de error 500 (Internal Server Error)
            return response()->json([
                'error' => 'Error interno del servidor al procesar la solicitud.',
                'message' => $e->getMessage() // Esto es útil para depuración, pero se debe evitar en producción.
            ], 500);
        }
    }
}