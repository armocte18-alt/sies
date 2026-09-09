<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'sucursal_id', 'limite_existencia_caja', 'volumen_total',
    'cantidad_situada', 'ingreso_estimado', 'gasto_total',
])]
class SucursalFinanza extends Model
{
    use HasFactory;

    protected $table = 'sucursal_finanzas';

    protected function casts(): array
    {
        return [
            'limite_existencia_caja' => 'decimal:2',
            'cantidad_situada' => 'decimal:2',
            'ingreso_estimado' => 'decimal:2',
            'gasto_total' => 'decimal:2',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    /**
     * Balance = ingreso estimado - gasto total.
     * Se calcula, no se almacena, para evitar datos desincronizados.
     */
    protected function balance(): Attribute
    {
        return Attribute::make(
            get: fn () => round((float) $this->ingreso_estimado - (float) $this->gasto_total, 2),
        );
    }

    protected function estatusFinanciero(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->balance >= 0 ? 'superavitaria' : 'deficitaria',
        );
    }
}
