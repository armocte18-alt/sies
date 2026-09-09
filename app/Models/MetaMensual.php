<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'linea_negocio', 'anio', 'tipo', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
    'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre',
    'meta_anual', 'producto_claves_vinculadas',
])]
class MetaMensual extends Model
{
    protected $table = 'metas_mensuales';

    public const MESES = [
        'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre',
    ];

    protected function casts(): array
    {
        return array_fill_keys([...self::MESES, 'meta_anual'], 'decimal:2');
    }
}
