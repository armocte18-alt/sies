<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'solicitante_id', 'numero_empleado', 'area', 'area_otro', 'fecha_salida_desde',
    'fecha_salida_hasta', 'destinos', 'motivo', 'estatus', 'autorizado_por',
    'autorizado_at', 'motivo_rechazo',
])]
class SolicitudVehiculo extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_vehiculos';

    public const AREA_LABELS = [
        'gerencia_estatal_cdmx' => 'Gerencia Estatal CDMX',
        'areas_centrales_disfo' => 'Áreas Centrales DISFO',
        'otro' => 'Otro',
    ];

    public const ESTATUS_LABELS = [
        'pendiente' => 'Pendiente',
        'autorizada' => 'Autorizada',
        'rechazada' => 'Rechazada',
        'finalizada' => 'Finalizada',
    ];

    protected function casts(): array
    {
        return [
            'fecha_salida_desde' => 'datetime',
            'fecha_salida_hasta' => 'datetime',
            'autorizado_at' => 'datetime',
            'destinos' => 'array',
        ];
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function autorizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autorizado_por');
    }

    public function detalle(): HasOne
    {
        return $this->hasOne(SolicitudVehiculoDetalle::class, 'solicitud_id');
    }
}
