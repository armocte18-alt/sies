<?php

namespace App\Models;

use App\Models\Concerns\HasNormalizedCase;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'nombre', 'puesto', 'dependencia', 'adscripcion', 'domicilio',
    'telefonos', 'correos', 'observaciones', 'activo', 'created_by', 'updated_by',
])]
class PersonalExterno extends Model
{
    use HasFactory, HasNormalizedCase;

    protected $table = 'personal_externos';

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'telefonos' => 'array',
            'correos' => 'array',
        ];
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
