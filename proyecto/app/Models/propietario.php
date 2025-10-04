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

    public function viviendas()
    {
        return $this->hasMany(Vivienda::class, 'id_propie');
    }
}
