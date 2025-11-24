<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class propietario extends Model
{
    use HasFactory;

    protected $table = 'propietarios';
    protected $primaryKey = 'id_propie';
    public $timestamps = false;

    protected $fillable = [
        'nombre_propie',
        'apellido_propie',
        'cedula_propie',
        'telefono',
    ];

    // Relación: Un propietario tiene muchas viviendas
    public function viviendas()
    {
        return $this->hasMany(Vivienda::class, 'id_propie');
    }

    // Mutadores para formatear los datos antes de guardarlos en la base de datos

    // 1. NOMBRE: Guardar en formato título
    public function setNombrePropieAttribute($value)
    {
        $this->attributes['nombre_propie'] = ucwords(strtolower($value));
    }

    // 2. APELLIDO: Guardar en formato título
    public function setApellidoPropieAttribute($value)
    {
        $this->attributes['apellido_propie'] = ucwords(strtolower($value));
    }

    // 3. CÉDULA: Limpiar y guardar solo números
    public function setCedulaPropieAttribute($value)
    {
        $cedulaLimpia = (string) $value;
        $cedulaLimpia = str_replace(['.', '-', ' '], '', $cedulaLimpia);
        $this->attributes['cedula_propie'] = $cedulaLimpia;
    }

    // 4. TELÉFONO: Limpiar y guardar solo números
    public function setTelefonoAttribute($value)
    {
        $telefonoLimpio = (string) $value;
        $telefonoLimpio = str_replace(['.', '-', ' '], '', $telefonoLimpio);
        $this->attributes['telefono'] = $telefonoLimpio;
    }
}
