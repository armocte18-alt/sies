<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'producto_id', 'numero_tarjeta', 'cuenta', 'fecha_recepcion', 'usuario_recibe',
    'estatus', 'fecha_asignacion', 'fecha_renominacion', 'destino_sucursal_id',
    'otro_destino_descripcion', 'usuario_asigna', 'numero_oficio',
])]
class TarjetaInventario extends Model
{
    use HasFactory;

    protected $table = 'tarjetas_inventario';

    public const ESTATUS_LABELS = [
        'en_stock' => 'En stock',
        'activa' => 'Activa',
        'renominada' => 'Renominada',
    ];

    protected function casts(): array
    {
        return [
            'fecha_recepcion' => 'date',
            'fecha_asignacion' => 'date',
            'fecha_renominacion' => 'date',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(TarjetaProducto::class, 'producto_id');
    }

    public function destinoSucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'destino_sucursal_id');
    }

    public function historial(): HasMany
    {
        return $this->hasMany(TarjetaHistorial::class, 'tarjeta_id')->latest();
    }

    public function destinoTexto(): ?string
    {
        if ($this->otro_destino_descripcion) {
            return 'Coordinación vinculada: '.$this->otro_destino_descripcion;
        }

        return $this->destinoSucursal?->etiqueta;
    }
}
