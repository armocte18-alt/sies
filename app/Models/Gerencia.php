<?php

namespace App\Models;

use App\Models\Concerns\HasNormalizedCase;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nombre', 'coordinacion', 'extension', 'correo_finabien', 'correo_sigitel',
    'comite', 'observaciones', 'activo', 'created_by', 'updated_by',
])]
class Gerencia extends Model
{
    use HasFactory, HasNormalizedCase;

    protected $table = 'gerencias';

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function areasCentrales(): HasMany
    {
        return $this->hasMany(AreaCentral::class);
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
