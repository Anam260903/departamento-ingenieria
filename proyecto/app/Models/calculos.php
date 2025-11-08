<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class calculos extends Model
{
    protected $table = 'calculos';
    protected $primaryKey = 'id_calculo';

    protected $fillable = [
        'codigo_calculo',
        'contenido',
        'fecha_creacion',
        'id_cate', // Clave foránea a categoria
        'id_user', // Clave foránea a usuario
    ];

    //Relación muchos a muchos con INFORME a través de la tabla calculo_informes
    public function informes()
    {
        return $this->belongsToMany(
            Informe::class,
            'calculo_informes',
            'id_calculo',
            'id_inf'
        );
    }
}
