<?php

namespace App\Models;

use App\Models\Concerns\HasNormalizedCase;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'nombre', 'puesto', 'adscripcion', 'gerencia_id', 'telefono', 'extension',
    'correo_finabien', 'correo_sigitel', 'observaciones', 'activo', 'created_by', 'updated_by',
])]
class AreaCentral extends Model
{
    use HasFactory, HasNormalizedCase;

    protected $table = 'areas_centrales';

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function gerencia(): BelongsTo
    {
        return $this->belongsTo(Gerencia::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
