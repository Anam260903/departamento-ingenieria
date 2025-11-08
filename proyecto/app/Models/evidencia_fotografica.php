<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class evidencia_fotografica extends Model
{
    protected $table = 'evidencia_fotografica';

    protected $primaryKey = 'id_evid';

    protected $fillable = [
        'ruta_archivo',
        'id_inf', // Clave foránea a informe
    ];

    // Relación: Una evidencia fotográfica pertenece a un informe
    public function informe()
    {
        return $this->belongsTo(Informe::class, 'id_inf', 'id_inf');
    }
}