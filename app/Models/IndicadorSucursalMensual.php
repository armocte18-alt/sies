<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'sucursal_id', 'clave_sucursal_legacy', 'nombre_sucursal_legacy', 'mes', 'anio',
    'edo', 'region', 'volumen_total', 'cantidad_situada', 'ingresos_total', 'gasto_total',
    'balance', 'rentabilidad', 'productividad', 'total_empleados', 'dias_laborados',
    'gasto_traslado_valores', 'gasto_servicios_basicos', 'numero_oficio_carga',
])]
class IndicadorSucursalMensual extends Model
{
    protected $table = 'indicadores_sucursal_mensuales';

    protected function casts(): array
    {
        return [
            'cantidad_situada' => 'decimal:2',
            'ingresos_total' => 'decimal:2',
            'gasto_total' => 'decimal:2',
            'balance' => 'decimal:2',
            'rentabilidad' => 'decimal:2',
            'productividad' => 'decimal:2',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }
}
