<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'numero', 'asunto', 'fecha_aplicacion', 'ambito', 'enlace_externo',
    'archivo_path', 'tips', 'creado_por', 'activo', 'registrado_por',
])]
class Circular extends Model
{
    use HasFactory;

    protected $table = 'circulares';

    protected function casts(): array
    {
        return [
            'fecha_aplicacion' => 'date',
            'activo' => 'boolean',
        ];
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function archivoUrl(): ?string
    {
        return $this->archivo_path ? Storage::disk('public')->url($this->archivo_path) : null;
    }
}
