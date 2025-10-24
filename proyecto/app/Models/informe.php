<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Informe extends Model
{
    use HasFactory;

    protected $table = 'informes';
    protected $primaryKey = 'id_inf';

    protected $fillable = [
        'fecha_inf',
        'comunidad',
        'antecedentes',
        'planteamiento',
        'resultados',
        'recomendacion',
        'id_insp', // Clave foránea a inspeccion
    ];

    // Relación: Un informe pertenece a una inspección
    public function inspeccion()
    {
        return $this->belongsTo(Inspeccion::class, 'id_insp', 'id_insp');
    }
}