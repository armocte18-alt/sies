<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre'])]
class Alcaldia extends Model
{
    use HasFactory;

    protected $table = 'alcaldias';

    public function ubicaciones(): HasMany
    {
        return $this->hasMany(SucursalUbicacion::class);
    }
}
