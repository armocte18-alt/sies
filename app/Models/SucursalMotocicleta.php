<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'sucursal_id', 'placa', 'marca', 'modelo', 'anio',
    'kilometraje_actual', 'estado', 'fecha_alta', 'notas',
])]
class SucursalMotocicleta extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['fecha_alta' => 'date'];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function cargasCombustible(): HasMany
    {
        return $this->hasMany(SucursalMotocicletaCombustible::class, 'motocicleta_id')->latest('fecha');
    }
}
