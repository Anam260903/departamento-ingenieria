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
        'id_rol', // Clave foránea a rol
    ];

    // Relación: Un usuario pertenece a un rol
    public function rol()
    {
        return $this->belongsTo(Roles::class, 'id_rol');
    }

    // Relación: Un usuario tiene muchas preguntas de seguridad
    public function preguntasSeguridad()
    {
        return $this->hasMany(PreguntaSeguridad::class, 'id_user', 'id_user');
    }

    // Relación: Un usuario tiene muchas inspecciones
    public function inspeccionesPendientes()
    {
        return $this->hasMany(Inspeccion::class, 'id_user')->where('estado_insp', 0);
    }

    // Campos que deben ocultarse al serializar el modelo
    protected $hidden = [
        'password',
    ];

    // Mutadores para formatear los datos antes de guardarlos en la base de datos
    
    // 1. CORREO: Guardar siempre en minúsculas
    public function setCorreoAttribute($value)
    {
        $this->attributes['correo'] = strtolower($value);
    }

    // 2. CÉDULA: Limpiar y guardar solo números
    public function setCedulaUserAttribute($value)
    {
        $cedulaLimpia = (string) $value;
        $cedulaLimpia = str_replace(['.', '-', ' '], '', $cedulaLimpia);
        $this->attributes['cedula_user'] = $cedulaLimpia;
    }

    // 3. NOMBRE: Guardar en formato título
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = ucwords(strtolower($value));
    }

    // 4. APELLIDO: Guardar en formato título
    public function setApellidoAttribute($value)
    {
        $this->attributes['apellido'] = ucwords(strtolower($value));
    }
}