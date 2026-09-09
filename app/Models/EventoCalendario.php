<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'nombre', 'fecha_inicio', 'fecha_fin', 'hora_inicio', 'hora_fin', 'ubicacion',
    'tipo_asociado', 'asociado_nombre', 'color', 'invitados', 'creado_por_nombre',
    'registrado_por', 'notas', 'grupo_recurrencia_id',
])]
class EventoCalendario extends Model
{
    use HasFactory;

    protected $table = 'eventos_calendario';

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'invitados' => 'array',
        ];
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
