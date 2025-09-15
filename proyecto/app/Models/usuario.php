<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'cedula_user',
        'nombre',
        'apellido',
        'correo',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Get the e-mail address that should be used for password reset.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->correo;
    }

    public function getAuthIdentifierName()
    {
        return 'correo';
    }
}