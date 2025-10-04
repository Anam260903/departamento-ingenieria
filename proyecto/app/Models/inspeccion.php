<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspeccion extends Model
{
    use SoftDeletes;

    protected $table = 'inspecciones';
    protected $primaryKey = 'id_insp';
    public $timestamps = false;

    protected $fillable = [
        'fecha_insp',
        'estado_insp',
        'observacion',
        'id_user', // FK de usuario (quién registra la inspección)
        'id_viv',  // FK de vivienda
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_user');
    }

    public function vivienda()
    {
        return $this->belongsTo(Vivienda::class, 'id_viv');
    }

    public function informe()
    {
        return $this->hasOne(Informe::class, 'id_insp');
    }
}