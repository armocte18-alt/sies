<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['motocicleta_id', 'fecha', 'litros', 'monto', 'kilometraje', 'notas', 'created_by'])]
class SucursalMotocicletaCombustible extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'litros' => 'decimal:2',
            'monto' => 'decimal:2',
        ];
    }

    public function motocicleta(): BelongsTo
    {
        return $this->belongsTo(SucursalMotocicleta::class, 'motocicleta_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
