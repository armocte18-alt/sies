<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'sucursal_id', 'tipo_inmueble', 'tipo_contrato_posesion', 'superficie_m2', 'medidas',
    'fecha_inicio_contrato', 'fecha_fin_contrato', 'monto_renta_mensual',
    'propietario_arrendador', 'numero_escritura_contrato',
    'cuenta_proteccion_civil', 'numero_dictamen_proteccion_civil', 'vigencia_proteccion_civil',
    'tipo_caja_fuerte', 'modelo_caja_fuerte', 'numero_inventario_caja_fuerte', 'caja_fuerte_tiene_llave',
    'observaciones',
])]
class SucursalInmueble extends Model
{
    use HasFactory;

    protected $table = 'sucursal_inmuebles';

    protected function casts(): array
    {
        return [
            'fecha_inicio_contrato' => 'date',
            'fecha_fin_contrato' => 'date',
            'superficie_m2' => 'decimal:2',
            'monto_renta_mensual' => 'decimal:2',
            'cuenta_proteccion_civil' => 'boolean',
            'vigencia_proteccion_civil' => 'date',
            'caja_fuerte_tiene_llave' => 'boolean',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }
}
