<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str; // Importar la Facade Str

class CedulaVenezolana implements ValidationRule
{
    /**
     * Define la lógica de validación para la cédula venezolana.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 1. Limpieza y validación inicial de formato (solo números y longitud).
        // Nos aseguramos de que el valor sea estrictamente el número.
        $cedula = Str::replace(['.', ','], '', $value);

        // Validación de formato: de 6 a 8 dígitos (rango más común) y evitar ceros o patrones simples.
        if (!preg_match('/^(?!0{6,8}$)(\d{6,8})$/', $cedula)) {
            $fail('La Cédula de Identidad debe tener entre 6 y 8 dígitos, y no puede ser un número ficticio.');
            return;
        }

        // 2. Llamada a la API de Verificación (CedulaVE-API)
        try {
            // URL de la API: https://cedulave.vercel.app/api/v1/cedula
            $response = Http::timeout(5)->get('https://api.megacreativo.com/public/cedula-ve/v1', [
                // El campo que pide la API es 'cedula'
                'cedula' => $cedula,
            ]);

            // 3. Verificación de la Respuesta de la API

            // Si falla la conexión o devuelve un estado de error HTTP
            if ($response->failed()) {
                \Log::error("Fallo la verificación de C.I. para {$cedula}: Status " . $response->status());
                $fail('Error de conexión con el servicio de validación de cédulas. Intente más tarde.');
                return;
            }

            $data = $response->json();

            // La API de CedulaVE-API devuelve 'status': 'ok' o 'error'.
            // Si devuelve 'error', significa que la cédula no existe.
            if (isset($data['status']) && $data['status'] === 'error') {
                $fail('La Cédula de Identidad ingresada no se encuentra registrada en el CNE.');
                return;
            }

            // Opcionalmente, puedes verificar que se haya retornado el campo 'nombre' para asegurar datos completos
            if (!isset($data['nombre'])) {
                $fail('La Cédula de Identidad retornó datos incompletos. Intente de nuevo.');
            }

        } catch (\Exception $e) {
            // Manejo de errores de red (ej. timeout, DNS error)
            \Log::error("Excepción al verificar C.I.: " . $e->getMessage());
            $fail('Hubo un problema de red al verificar la cédula. Intente de nuevo.');
        }
    }
}
