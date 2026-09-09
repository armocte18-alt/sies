<?php

namespace App\Models;

use App\Models\Concerns\HasNormalizedCase;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nombre', 'descripcion', 'orden', 'activo'])]
class Labor extends Model
{
    use HasFactory, HasNormalizedCase;

    protected $table = 'labores';

    protected $normalizedCase = ['nombre', 'descripcion'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }
}
