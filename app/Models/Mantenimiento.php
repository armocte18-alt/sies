<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'sucursal_id', 'tipo_mantenimiento_id', 'titulo', 'descripcion', 'prioridad', 'estatus',
    'fecha_programada_inicio', 'fecha_programada_fin', 'fecha_inicio_real', 'fecha_fin_real',
    'costo_mano_obra', 'costo_materiales', 'comentario_cierre', 'creado_por', 'cerrado_por',
])]
class Mantenimiento extends Model
{
    use HasFactory, SoftDeletes;

    public const PRIORIDADES = [
        'baja' => 'Baja',
        'media' => 'Media',
        'alta' => 'Alta',
        'urgente' => 'Urgente',
    ];

    public const ESTATUS_LABELS = [
        'pendiente' => 'Pendiente',
        'programado' => 'Programado',
        'en_proceso' => 'En proceso',
        'completado' => 'Completado',
        'cancelado' => 'Cancelado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_programada_inicio' => 'date',
            'fecha_programada_fin' => 'date',
            'fecha_inicio_real' => 'datetime',
            'fecha_fin_real' => 'datetime',
            'costo_mano_obra' => 'decimal:2',
            'costo_materiales' => 'decimal:2',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoMantenimiento::class, 'tipo_mantenimiento_id');
    }

    public function materiales(): HasMany
    {
        return $this->hasMany(MantenimientoMaterial::class);
    }

    public function personal(): BelongsToMany
    {
        return $this->belongsToMany(Empleado::class, 'mantenimiento_personal')
            ->withPivot('id', 'rol_en_mantenimiento')
            ->withTimestamps();
    }

    public function bitacora(): HasMany
    {
        return $this->hasMany(MantenimientoBitacora::class)->latest();
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function cerrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cerrado_por');
    }

    public function costoTotal(): float
    {
        return (float) $this->costo_mano_obra + (float) $this->costo_materiales;
    }

    public function enRiesgo(): bool
    {
        return in_array($this->estatus, ['pendiente', 'programado', 'en_proceso'], true)
            && $this->fecha_programada_fin?->isPast();
    }

    public function recalcularCostoMateriales(): void
    {
        $this->update(['costo_materiales' => $this->materiales()->sum('costo_total')]);
    }
}
