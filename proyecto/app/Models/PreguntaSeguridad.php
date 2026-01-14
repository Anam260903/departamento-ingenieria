<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class PreguntaSeguridad extends Model
{
    use HasFactory;

    protected $table = 'preguntas_seguridad';
    protected $primaryKey = 'id_preg';

    protected $fillable = [
        'id_user', // Clave foránea a usuario
        'pregunta',
        'respuesta',
    ];

    // Relación: Una pregunta de seguridad pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_user', 'id_user');
    }

}
