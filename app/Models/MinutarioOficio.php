<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'consecutivo', 'anio', 'nomenclatura', 'asunto', 'dirigido_a', 'fecha_emision',
    'nombre_solicitante', 'registrado_por', 'es_cancelado', 'ya_escaneado',
    'motivo_cancelacion', 'cancelado_por_nombre', 'fecha_cancelacion',
])]
class MinutarioOficio extends Model
{
    use HasFactory;

    protected $table = 'minutario_oficios';

    protected function casts(): array
    {
        return [
            'fecha_emision' => 'date',
            'fecha_cancelacion' => 'datetime',
            'es_cancelado' => 'boolean',
            'ya_escaneado' => 'boolean',
        ];
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
