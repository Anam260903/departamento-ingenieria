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
     * Determine whether the user can view any inspections (Scope before query).
     * This is only used in index() and is handled directly in the Controller.
     */
    public function viewAny(Usuario $user): bool
    {
        // Todos los usuarios autenticados pueden ver la lista, 
        // pero la restricción de datos se aplica en el controlador.
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Los administradores pueden ver cualquiera. Los usuarios solo los propios.
     *
     * @param  \App\Models\Usuario  $user
     * @param  \App\Models\Inspeccion  $inspeccion
     * @return \Illuminate\Auth\Access\Response|bool
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
     * Determine whether the user can update the model.
     * Sigue la misma lógica que 'view'.
     *
     * @param  \App\Models\Usuario  $user
     * @param  \App\Models\Inspeccion  $inspeccion
     * @return \Illuminate\Auth\Access\Response|bool
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
     * Determine whether the user can delete the model.
     * Sigue la misma lógica que 'view' y 'update'.
     *
     * @param  \App\Models\Usuario  $user
     * @param  \App\Models\Inspeccion  $inspeccion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Usuario $user, Inspeccion $inspeccion): Response
    {
        // Un administrador (id_rol = 1) siempre puede eliminar
        if ($user->id_rol === 1) {
            return Response::allow();
        }

        // Un usuario normal (id_rol = 2) solo puede eliminar si el id_user coincide
        return Response::deny('Solo los administradores pueden eliminar inspecciones.');
    }

    /**
     * Determine whether the user can create inspections.
     *
     * @param  \App\Models\Usuario  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Usuario $user): Response
    {
        if ($user->id_rol === 1) {
            return Response::allow();
        }

        return Response::deny('Solo los administradores pueden crear inspecciones.');
    }
        
}