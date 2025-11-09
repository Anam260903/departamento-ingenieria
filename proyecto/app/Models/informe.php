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
        'materials_info',
        'id_insp', // Clave foránea a inspeccion
    ];

    // Relación: Un informe pertenece a una inspección
    public function inspeccion()
    {
        return $this->belongsTo(Inspeccion::class, 'id_insp', 'id_insp');
    }

    // Relación: Un informe tiene muchas evidencias fotográficas
    public function imagenes()
    {
        return $this->hasMany(evidencia_fotografica::class, 'id_inf', 'id_inf');
    }


    //Relación muchos a muchos con CALCULOS a través de la tabla calculo_informes
    public function calculos()
    {
        return $this->belongsToMany(
            Calculos::class,
            'calculo_informes',
            'id_inf',
            'id_calculo'
        );
    }
}