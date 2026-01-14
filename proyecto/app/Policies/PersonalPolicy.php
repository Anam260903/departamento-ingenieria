<?php

namespace App\Policies;

use App\Models\Usuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class PersonalPolicy
{
    use HandlesAuthorization;

    /**
     * Otorga acceso total al Administrador (id_rol === 1) antes de cualquier otra verificación.
     */
    public function before(Usuario $user, $ability)
    {
        if ($user->id_rol === 1) {
            return true;
        }
        return null;
    }

    /**
     * Determina si el usuario puede ver la lista de personal (index).
     */
    public function viewAny(Usuario $user)
    {
        // El Rol 2 Inspector es denegado
        return false;
    }

    /**
     * Determina si el usuario puede manipular (editar, actualizar, cambiar estado, asignar inspección) a otro usuario.
     */
    public function update(Usuario $user, Usuario $targetUser)
    {
        // El Rol 2 Inspector es denegado
        return false;
    }

    /**
     * Determina si el usuario puede ver la lista de recursos disponibles.
     */
    public function viewAvailableRecursos(Usuario $user)
    {
        // El Rol 2 Inspector es denegado
        return false;
    }

    /**
     * Determina si el usuario puede asignar un recurso a otro usuario.
     */
    public function assignRecurso(Usuario $user, Usuario $targetUser)
    {
        // El Rol 2 Inspector es denegado
        return false;
    }
}