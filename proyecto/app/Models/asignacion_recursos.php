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

    // Esta relación es para obtener el usuario que recibió la asignación.
    public function usuario()
    {
        // belongsTo(ModeloUsuario, llave_foranea_en_esta_tabla, llave_primaria_en_Usuario)
        // Asumo que tu modelo se llama App\Models\Usuario y su PK es 'id_user'.
        return $this->belongsTo(Usuario::class, 'id_user', 'id_user');
    }
}
