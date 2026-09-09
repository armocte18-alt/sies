<?php

namespace App\Models;

use App\Models\Concerns\HasNormalizedCase;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['consecutivo', 'nivel_salarial', 'sueldo_base', 'compensacion_garantizada', 'observaciones', 'activo'])]
class NivelSalarial extends Model
{
    use HasFactory, HasNormalizedCase;

    protected $table = 'niveles_salariales';

    protected function casts(): array
    {
        return [
            'sueldo_base' => 'decimal:2',
            'compensacion_garantizada' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }
}
