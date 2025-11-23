<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class asignacion_recursos extends Model
{
    protected $table = 'asignacion_recursos';
    protected $primaryKey = 'id_asignacion';

    protected $fillable = [
        'id_user',
        'id_recurso',
        'fecha_asignacion',
        'fecha_devolucion',
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'fecha_devolucion' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_user', 'id_user');
    }

    public function recurso()
    {
        return $this->belongsTo(Recursos::class, 'id_recurso', 'id_recurso');
    }
}