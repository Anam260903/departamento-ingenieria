<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Informe extends Model
{

    use SoftDeletes;
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
        'materials_info',
        'id_insp', // Clave foranea a inspección
        'id_calculo', // Clave foranea a calculos
    ];

    // Relación: Un informe pertenece a una inspección
    public function inspeccion()
    {
        return $this->belongsTo(Inspeccion::class, 'id_insp', 'id_insp');
    }

    // Relación: Un informe pertenece a un cálculo (Uno a Muchos)
    public function calculos()
    {
        return $this->belongsTo(Calculos::class, 'id_calculo', 'id_calculo');
    }

    // Relación: Un informe tiene muchas evidencias fotográficas
    public function imagenes()
    {
        return $this->hasMany(evidencia_fotografica::class, 'id_inf', 'id_inf');
    }

}