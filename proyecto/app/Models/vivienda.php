<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class vivienda extends Model
{
    use HasFactory;

    protected $table = 'viviendas';
    protected $primaryKey = 'id_viv';
    public $timestamps = false; // No usamos created_at y updated_at en esta tabla

    protected $fillable = [
        'direccion',
        'id_propie',
    ];

    public function propietario()
    {
        return $this->belongsTo(Propietario::class, 'id_propie');
    }

    public function inspecciones()
    {
        return $this->hasMany(Inspeccion::class, 'id_viv');
    }
}
