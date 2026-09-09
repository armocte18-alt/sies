<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'slug', 'color', 'activo', 'orden'])]
class TipoMantenimiento extends Model
{
    use HasFactory;

    protected $table = 'tipos_mantenimiento';

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class);
    }
}
