<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';
    protected $primaryKey = 'id_notificacion';
    public $timestamps = true;
    
    protected $fillable = [
        'id_user', // Clave foránea a usuario
        'mensaje',
        'tipo',
        'leida',
    ];

    // Relación: Muchas notificaciones pertenecen a un usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_user', 'id_user');
    }
}
