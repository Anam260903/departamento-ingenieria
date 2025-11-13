<?php

namespace App\Policies;

use App\Models\Usuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashboardPolicy
{
    use HandlesAuthorization;

    /**
     * Otorga acceso total al Administrador (id_rol === 1) antes de cualquier otra verificación.
     */
    public function before(Usuario $user, $ability)
    {
        // Los administradores tienen acceso completo.
        if ($user->id_rol === 1) {
            return true;
        }
        // Si no es administrador, se continúa con la verificación de los demás métodos.
        return null;
    }

    /**
     * Determina si el usuario puede ver la página del Dashboard.
     * Dado que el Admin pasa por 'before', esta verificación es para otros roles (id_rol=2).
     */
    public function viewDashboard(Usuario $user)
    {
        // Permite que el rol 2 acceda al dashboard para ver sus datos.
        return $user->id_rol === 2;
    }
}
