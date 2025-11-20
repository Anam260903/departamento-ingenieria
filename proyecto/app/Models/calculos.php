<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class calculos extends Model
{

    use HasFactory;
    protected $table = 'calculos';
    protected $primaryKey = 'id_calculo';

    protected $fillable = [
        'nombre_calculo',
        'contenido',
    ];

    // Relación: Un informe puede tener muchos calculos
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
