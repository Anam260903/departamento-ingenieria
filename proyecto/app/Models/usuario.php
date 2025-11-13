<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'cedula_user',
        'nombre',
        'apellido',
        'correo',
        'password',
        'profesion',
        'estado_user',
        'id_rol', // FK para de rol
    ];

    // Relación: Un usuario pertenece a un rol
    public function rol()
    {
        return $this->belongsTo(Roles::class, 'id_rol');
    }

    // Campos que deben ocultarse al serializar el modelo
    protected $hidden = [
        'password',
    ];

    //Funcion para enviar el correo de restablecimiento de contraseña
    public function getEmailForPasswordReset()
    {
        return $this->correo;
    }
}