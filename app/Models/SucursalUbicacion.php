<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'sucursal_id', 'calle', 'num_ext', 'num_int', 'colonia', 'alcaldia_id',
    'codigo_postal', 'entre_calle_1', 'entre_calle_2', 'referencia_visual',
    'latitud', 'longitud', 'clave_geografica_inegi',
])]
class SucursalUbicacion extends Model
{
    use HasFactory;

    protected $table = 'sucursal_ubicaciones';

    protected function casts(): array
    {
        return [
            'latitud' => 'decimal:6',
            'longitud' => 'decimal:6',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function alcaldia(): BelongsTo
    {
        return $this->belongsTo(Alcaldia::class);
    }
}
