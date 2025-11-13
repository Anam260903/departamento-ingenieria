<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class vivienda extends Model
{
    use HasFactory;

    protected $table = 'viviendas';
    protected $primaryKey = 'id_viv';
    public $timestamps = false;

    protected $fillable = [
        'direccion', 
        'id_propie', 
        'caracteristicas', 
        'latitud',
        'longitud',
        'map_image_file',
    ];

    // Relación: Una vivienda pertenece a un propietario
    public function propietario()
    {
        return $this->belongsTo(Propietario::class, 'id_propie');
    }

    // Relación: Una vivienda tiene muchas inspecciones
    public function inspecciones()
    {
        return $this->hasMany(Inspeccion::class, 'id_viv');
    }
}
