<?php

namespace App\Policies;

use App\Models\Usuario; // Asumimos que su modelo de usuario se llama 'User'
use App\Models\Informe;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class InformePolicy
{
    use HandlesAuthorization;

    /**
     * Se ejecuta antes que cualquier otro método de la Policy.
     * Otorga acceso total al Administrador (id_rol === 1).
     */
    public function before(Usuario $user, $ability)
    {
        // Los administradores tienen acceso a todas las acciones
        if ($user->id_rol === 1) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any informes (la lista).
     * El Administrador ya está cubierto por 'before'. El usuario normal
     * puede acceder a la lista, pero el controlador filtrará los resultados.
     */
    public function viewAny(Usuario $user)
    {
        // Si no es Admin (id_rol=1), se asume id_rol=2, y se permite.
        return $user->id_rol === 2;
    }

    /**
     * Determine whether the user can view a single informe.
     * El usuario normal (id_rol === 2) solo puede ver los informes que creó.
     * Se asume que el Informe tiene cargada la relación 'inspeccion' y que
     * la inspección tiene el 'id_user' del creador.
     */
    public function view(Usuario $user, Informe $informe)
    {
        return $informe->inspeccion->id_user === $user->id
            ? Response::allow()
            : Response::deny('No está autorizado para ver este informe.');
    }

    /**
     * Determine whether the user can create informes.
     * Se permite la creación al usuario normal (id_rol === 2).
     */
    public function create(Usuario $user)
    {
        return $user->id_rol === 2;
    }

    /**
     * Determine whether the user can update the informe.
     * El usuario normal (id_rol === 2) solo puede actualizar los informes que creó.
     */
    public function update(Usuario $user, Informe $informe)
    {
        return $informe->inspeccion->id_user === $user->id_user
            ? Response::allow()
            : Response::deny('No está autorizado para modificar este informe.');
    }

    /**
     * Determine whether the user can delete the informe.
     * REGLA CLAVE: El usuario normal (id_rol === 2) NO PUEDE ELIMINAR informes.
     */
    public function delete(Usuario $user, Informe $informe)
    {
        // Se deniega la acción de eliminación explícitamente.
        // El administrador (id_rol=1) pasa la verificación en 'before()'.
        return false;
    }

    /**
     * Método adicional para la descarga del PDF, que usa la misma lógica que 'view'.
     */
    public function download(Usuario $user, Informe $informe)
    {
        return $this->view($user, $informe);
    }
}