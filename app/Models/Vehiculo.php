<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'marca', 'modelo', 'anio', 'tipo', 'tipo_otro', 'placa', 'folio_tarjeta_circulacion',
    'vigencia_tarjeta_circulacion', 'folio_poliza_seguro', 'vigencia_poliza_seguro',
    'sucursal_id', 'kilometraje_actual', 'combustible_inicial', 'equipo_seguridad',
    'condicion_notas', 'fecha_ultima_verificacion', 'tipo_holograma', 'fecha_ultimo_servicio',
    'estatus',
])]
class Vehiculo extends Model
{
    use HasFactory;

    public const ESTATUS_LABELS = [
        'disponible' => 'Disponible',
        'asignado' => 'Asignado',
        'mantenimiento' => 'En mantenimiento',
        'fuera_de_servicio' => 'Fuera de servicio',
    ];

    protected function casts(): array
    {
        return [
            'vigencia_tarjeta_circulacion' => 'date',
            'vigencia_poliza_seguro' => 'date',
            'fecha_ultima_verificacion' => 'date',
            'fecha_ultimo_servicio' => 'date',
            'equipo_seguridad' => 'array',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function solicitudesDetalle(): HasMany
    {
        return $this->hasMany(SolicitudVehiculoDetalle::class);
    }

    public function descripcion(): string
    {
        return "{$this->marca} {$this->modelo} {$this->anio} ({$this->placa})";
    }
}
