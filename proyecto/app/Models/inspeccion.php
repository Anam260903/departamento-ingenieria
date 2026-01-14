<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspeccion extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'inspecciones';
    protected $primaryKey = 'id_insp';
    public $timestamps = false;

    protected $fillable = [
        'fecha_insp',
        'estado_insp',
        'observacion',
        'id_user', // Clave foránea a usuario
        'id_viv',  // Clave foránea a vivienda
    ];

    // Relación: Una inspección pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_user');
    }

    // Relación: Una inspección pertenece a una vivienda
    public function vivienda()
    {
        return $this->belongsTo(Vivienda::class, 'id_viv');
    }

    // Relación: Una inspección tiene un informe
    public function informe()
    {
        return $this->hasOne(Informe::class, 'id_insp');
    }
}