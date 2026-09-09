<?php

namespace App\Models;

use App\Models\Concerns\HasNormalizedCase;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nombre', 'descripcion', 'orden', 'activo'])]
class TipoNombramiento extends Model
{
    use HasFactory, HasNormalizedCase;

    protected $table = 'tipos_nombramiento';

    protected $normalizedCase = ['nombre', 'descripcion'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }
}
