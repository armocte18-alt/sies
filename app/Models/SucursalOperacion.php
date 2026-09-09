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

    public function resumenHorario(): ?string
    {
        return $this->resumen($this->dias_laborables, $this->hora_apertura_publico, $this->hora_cierre_publico);
    }

    public function resumenGuardia(): ?string
    {
        // apertura_guardia/cierre_guardia suelen traer 00:00 por defecto en
        // filas donde nunca se configuró guardia; sin días no hay guardia
        // real que mostrar, aunque las horas no vengan nulas.
        if (! $this->dias_guardia) {
            return null;
        }

        return $this->resumen($this->dias_guardia, $this->apertura_guardia, $this->cierre_guardia);
    }

    private function resumen(?string $dias, $desde, $hasta): ?string
    {
        $horas = $desde && $hasta ? "{$desde->format('H:i')} - {$hasta->format('H:i')} hrs" : null;

        return collect([$dias, $horas])->filter()->implode(' · ') ?: null;
    }
}
