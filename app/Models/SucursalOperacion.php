<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'sucursal_id', 'dias_laborables', 'hora_apertura_publico', 'hora_cierre_publico',
    'hora_inicio_labores_interno', 'hora_fin_labores_interno', 'tipo_poblacion',
    'tipo_inmueble', 'comunicacion', 'telefono', 'reparto_activo', 'enrutamiento',
    'dias_guardia', 'apertura_guardia', 'cierre_guardia',
])]
class SucursalOperacion extends Model
{
    use HasFactory;

    protected $table = 'sucursal_operaciones';

    protected function casts(): array
    {
        return [
            'reparto_activo' => 'boolean',
            'hora_apertura_publico' => 'datetime',
            'hora_cierre_publico' => 'datetime',
            'hora_inicio_labores_interno' => 'datetime',
            'hora_fin_labores_interno' => 'datetime',
            'apertura_guardia' => 'datetime',
            'cierre_guardia' => 'datetime',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }
}
