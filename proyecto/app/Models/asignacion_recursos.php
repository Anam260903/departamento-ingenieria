<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class asignacion_recursos extends Model
{
    protected $table = 'asignacion_recursos';
    
    protected $fillable = [
        'fecha_asignacion',
        'fecha_devolucion',
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        
    ];

    // Relación para obtener el usuario que recibió la asignación.
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_user', 'id_user');
    }

    // Relación para obtener el recurso que se asignó.
    public function recurso()
    {
        return $this->belongsTo(Recursos::class, 'id_recurso', 'id_recurso');
    }
}
