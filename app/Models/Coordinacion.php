<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['clave', 'nombre'])]
class Coordinacion extends Model
{
    use HasFactory;

    protected $table = 'coordinaciones';

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function empleados(): HasMany
    {
        return $this->hasMany(Empleado::class);
    }
}
