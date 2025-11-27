<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\Recursos;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecursosPolicy
{
    use HandlesAuthorization;

    /**
     * Otorga acceso total al Administrador
     */
    public function before(Usuario $user, $ability)
    {
        if ($user->id_rol === 1) {
            return true; // Administrador pasa todas las verificaciones
        }
        return null;
    }

    /**
     * Determina si el usuario puede ver la lista general de recursos.
     * Solo permitido si el usuario es el Administrador
     */
    public function viewAny(Usuario $user)
    {
        return false;
    }

    /**
     * Determina si el usuario puede ver el historial de asignaciones.
     * Permitido para ambos roles. El Rol 2 verá los datos filtrados en el controlador.
     */
    public function viewHistory(Usuario $user)
    {
        return $user->id_rol === 2;
    }

    /**
     * Determina si el usuario Rol 2 puede crear, editar o eliminar (gestión total).
     */
    public function manage(Usuario $user)
    {
        return false;
    }

    /**
     * Determina si el usuario puede llamar al método de devolución.
     */
    public function canReturn(Usuario $user)
    {
        return $user->id_rol === 2;
    }

    /**
     * Determina si el usuario puede descargar el listado general de recursos.
     */
    public function exportGeneralPDF(Usuario $user)
    {
        return false; // Denegado para id_rol=2
    }

    /**
     * Determina si el usuario puede descargar su propio historial de asignaciones
     * Se mantiene por claridad si se desea una granularidad extra, pero puede usarse 'viewHistory'.
     */
    public function exportHistoryPDF(Usuario $user)
    {
        return $user->id_rol === 2; // Permitido para id_rol=2 (el controlador filtra la data)
    }
}