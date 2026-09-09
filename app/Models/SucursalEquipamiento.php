<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'sucursal_id', 'num_computadoras', 'num_impresoras', 'num_servidores',
    'tiene_camaras', 'num_camaras', 'tiene_alarma', 'tiene_extintores', 'num_extintores',
    'proveedor_internet', 'tipo_enlace', 'velocidad_contratada', 'observaciones',
])]
class SucursalEquipamiento extends Model
{
    use HasFactory;

    protected $table = 'sucursal_equipamientos';

    protected function casts(): array
    {
        return [
            'tiene_camaras' => 'boolean',
            'tiene_alarma' => 'boolean',
            'tiene_extintores' => 'boolean',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }
}
