<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['mantenimiento_id', 'nombre_material', 'unidad', 'cantidad', 'costo_unitario', 'costo_total'])]
class MantenimientoMaterial extends Model
{
    use HasFactory;

    protected $table = 'mantenimiento_materiales';

    protected function casts(): array
    {
        return [
            'cantidad' => 'decimal:2',
            'costo_unitario' => 'decimal:2',
            'costo_total' => 'decimal:2',
        ];
    }

    public function mantenimiento(): BelongsTo
    {
        return $this->belongsTo(Mantenimiento::class);
    }
}
