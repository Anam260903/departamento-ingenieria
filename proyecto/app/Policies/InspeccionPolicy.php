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
     * Determina si el usuario puede ver las inspecciones.
     */
    public function viewAny(Usuario $user): bool
    {
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

        // Un usuario inspector (id_rol = 2) solo puede ver si el id_user coincide
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

        // Un usuario inspector (id_rol = 2) solo puede actualizar si el id_user coincide
        return $user->id_user === $inspeccion->id_user
            ? Response::allow()
            : Response::deny('No tienes permiso para editar esta inspección.');
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
     * Determinar si el usuario puede cancelar la asignación la inspección.
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
     * Determina si el usuario puede crear inspecciones.
     */
    public function create(Usuario $user): Response
    {
        if ($user->id_rol === 1) {
            return Response::allow();
        }

        return Response::deny('Solo los administradores pueden crear inspecciones.');
    }
        
}