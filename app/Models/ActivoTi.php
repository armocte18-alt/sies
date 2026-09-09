<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'sucursal_id', 'tipo', 'etiqueta_inventario', 'marca', 'modelo',
    'numero_serie', 'estado', 'fecha_asignacion',
])]
class ActivoTi extends Model
{
    use HasFactory;

    protected $table = 'activos_ti';

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'date',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }
}
