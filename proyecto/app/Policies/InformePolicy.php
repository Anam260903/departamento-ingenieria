<?php

namespace App\Policies;

use App\Models\Usuario;
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
     * Determina si el usuario puede ver algún informe
     * El usuario inspector puede acceder a la lista, pero el controlador filtrará los resultados.
     */
    public function viewAny(Usuario $user)
    {
        return $user->id_rol === 2;
    }

    /**
     * Determinar si el usuario puede ver un informe.
     * El usuario inspector (id_rol === 2) solo puede ver los informes que creó.
     */
    public function view(Usuario $user, Informe $informe)
    {
        return $informe->inspeccion->id_user === $user->id
            ? Response::allow()
            : Response::deny('No está autorizado para ver este informe.');
    }

    /**
     * Determinar si el usuario puede crear informes.
     * Se permite la creación al usuario inspector (id_rol === 2).
     */
    public function create(Usuario $user)
    {
        return $user->id_rol === 2;
    }

    /**
     * Determinar si el usuario puede actualizar al informe.
     * El usuario inspector (id_rol === 2) solo puede actualizar los informes que creó.
     */
    public function update(Usuario $user, Informe $informe)
    {
        return $informe->inspeccion->id_user === $user->id_user
            ? Response::allow()
            : Response::deny('No está autorizado para modificar este informe.');
    }

    /**
     * Determinar si el usuario puede eliminar el informe.
     * El usuario inspector (id_rol === 2) no puede eliminar informes.
     */
    public function delete(Usuario $user, Informe $informe)
    {
        return false;
    }

}