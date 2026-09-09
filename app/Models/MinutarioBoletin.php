<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'consecutivo', 'anio', 'nomenclatura', 'asunto', 'contenido', 'fecha_elaboracion',
    'fecha_vigor', 'dirigido_tipo', 'registrado_por', 'fecha_ultima_modificacion',
])]
class MinutarioBoletin extends Model
{
    use HasFactory;

    protected $table = 'minutario_boletines';

    public const COBERTURAS = [
        'todos' => 'A todo el personal de la Gerencia Estatal y sucursales de la CDMX',
        'gerencia' => 'Personal de la Gerencia',
        'sucursales' => 'Personal de Sucursales',
    ];

    protected function casts(): array
    {
        return [
            'fecha_elaboracion' => 'date',
            'fecha_vigor' => 'date',
            'fecha_ultima_modificacion' => 'datetime',
        ];
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
