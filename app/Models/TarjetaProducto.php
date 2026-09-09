<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'slug', 'descripcion'])]
class TarjetaProducto extends Model
{
    use HasFactory;

    protected $table = 'tarjetas_productos';

    public function inventario(): HasMany
    {
        return $this->hasMany(TarjetaInventario::class, 'producto_id');
    }
}
