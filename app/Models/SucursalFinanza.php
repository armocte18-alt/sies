<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['sucursal_id', 'limite_existencia_caja'])]
class SucursalFinanza extends Model
{
    use HasFactory;

    protected $table = 'sucursal_finanzas';

    protected function casts(): array
    {
        return [
            'limite_existencia_caja' => 'decimal:2',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }
}
