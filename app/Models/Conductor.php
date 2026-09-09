<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'empleado_id', 'nombre_completo', 'numero_empleado', 'area_adscripcion', 'numero_licencia',
    'tipo_licencia', 'es_permanente', 'vigencia_licencia', 'estatus',
])]
class Conductor extends Model
{
    use HasFactory;

    protected $table = 'conductores';

    protected function casts(): array
    {
        return [
            'vigencia_licencia' => 'date',
            'es_permanente' => 'boolean',
        ];
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function solicitudesDetalle(): HasMany
    {
        return $this->hasMany(SolicitudVehiculoDetalle::class);
    }
}
