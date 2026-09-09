<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['sucursal_id', 'tipo', 'talla', 'cantidad_total', 'cantidad_buen_estado', 'cantidad_danado', 'notas'])]
class SucursalEquipamientoReparto extends Model
{
    use HasFactory;

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }
}
