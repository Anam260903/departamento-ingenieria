<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class recursos extends Model
{

    use SoftDeletes;
    use HasFactory;
    protected $table = 'recursos';
    protected $primaryKey = 'id_recurso';

    protected $fillable = [
        'codigo',
        'nombre_rec',
        'descripcion',
        'observacion',
    ];
    public function asignaciones()
    {
        return $this->hasMany(asignacion_recursos::class, 'id_recurso', 'id_recurso');
    }

    /**
     * Determina si el recurso tiene una asignación activa (sin fecha de devolución).
     */
    public function estaAsignado(): bool
    {
        // Busca en la colección de asignaciones si existe alguna donde fecha_devolucion es NULL.
        return $this->asignaciones()
            ->whereNull('fecha_devolucion')
            ->exists();
    }
}
