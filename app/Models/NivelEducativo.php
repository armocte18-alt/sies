<?php

namespace App\Models;

use App\Models\Concerns\HasNormalizedCase;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nombre', 'descripcion', 'orden', 'activo'])]
class NivelEducativo extends Model
{
    use HasFactory, HasNormalizedCase;

    protected $table = 'niveles_educativos';

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }
}
