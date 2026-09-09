<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'solicitud_id', 'vehiculo_id', 'conductor_id', 'km_inicial', 'km_final', 'km_recorridos',
    'combustible_entrega', 'combustible_recepcion', 'carroceria_notas_previas',
    'carroceria_notas_nuevas', 'checklist_gato', 'checklist_llave_cruz',
    'checklist_reflejantes', 'checklist_extintor', 'fecha_hora_devolucion', 'observaciones_fallas',
])]
class SolicitudVehiculoDetalle extends Model
{
    use HasFactory;

    protected $table = 'solicitud_vehiculo_detalles';

    protected function casts(): array
    {
        return [
            'fecha_hora_devolucion' => 'datetime',
            'checklist_gato' => 'boolean',
            'checklist_llave_cruz' => 'boolean',
            'checklist_reflejantes' => 'boolean',
            'checklist_extintor' => 'boolean',
        ];
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(SolicitudVehiculo::class, 'solicitud_id');
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function conductor(): BelongsTo
    {
        return $this->belongsTo(Conductor::class);
    }

    public static function kmInicialSugerido(int $vehiculoId): int
    {
        $ultimo = static::where('vehiculo_id', $vehiculoId)->latest('id')->first();

        return $ultimo?->km_final ?? Vehiculo::find($vehiculoId)?->kilometraje_actual ?? 0;
    }
}
