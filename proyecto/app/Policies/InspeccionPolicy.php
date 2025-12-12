<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\Inspeccion;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class InspeccionPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver las inspecciones (Alcance antes de la consulta).
     */
    public function viewAny(Usuario $user): bool
    {
        // Todos los usuarios autenticados pueden ver la lista, 
        // pero la restricción de datos se aplica en el controlador.
        return true;
    }

    /**
     * Determinar si el usuario puede ver las inspecciones.
     * Los administradores pueden ver cualquiera. Los usuarios solo las propias.
     */
    public function view(Usuario $user, Inspeccion $inspeccion): Response
    {
        // Un administrador (id_rol = 1) siempre puede ver
        if ($user->id_rol === 1) {
            return Response::allow();
        }

        // Un usuario normal (id_rol = 2) solo puede ver si el id_user coincide
        return $user->id_user === $inspeccion->id_user
            ? Response::allow()
            : Response::deny('No tienes permiso para ver esta inspección.');
    }

    /**
     * Determinar si el usuario puede actualizar la inspeccion.
     */
    public function update(Usuario $user, Inspeccion $inspeccion): Response
    {
        // Un administrador (id_rol = 1) siempre puede actualizar
        if ($user->id_rol === 1) {
            return Response::allow();
        }

        // Un usuario normal (id_rol = 2) solo puede actualizar si el id_user coincide
        return $user->id_user === $inspeccion->id_user
            ? Response::allow()
            : Response::deny('No tienes permiso para editar esta inspección, no fuiste el responsable de su registro.');
    }

    /**
     * Determinar si el usuario puede eliminar la inspección.
     */
    public function delete(Usuario $user, Inspeccion $inspeccion): Response
    {
        // Un administrador (id_rol = 1) siempre puede eliminar
        if ($user->id_rol === 1) {
            return Response::allow();
        }

        return Response::deny('Solo los administradores pueden eliminar inspecciones.');
    }

    /**
     * Determinar si el cancelar la asignación la inspección.
     */
    public function reassign(Usuario $user, Inspeccion $inspeccion): Response
    {
        // Un administrador (id_rol = 1) siempre puede eliminar
        if ($user->id_rol === 1) {
            return Response::allow();
        }
        return Response::deny('Solo los administradores pueden cancelar asignaciones de inspecciones.');
    }

    /**
     * Determine whether the user can create inspections.
     */
    public function create(Usuario $user): Response
    {
        if ($user->id_rol === 1) {
            return Response::allow();
        }

        return Response::deny('Solo los administradores pueden crear inspecciones.');
    }
        
}