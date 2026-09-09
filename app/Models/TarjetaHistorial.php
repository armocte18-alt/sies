<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tarjeta_id', 'accion', 'estatus_anterior', 'estatus_nuevo', 'motivo', 'usuario'])]
class TarjetaHistorial extends Model
{
    protected $table = 'tarjetas_historial';

    public function tarjeta(): BelongsTo
    {
        return $this->belongsTo(TarjetaInventario::class, 'tarjeta_id');
    }
}
