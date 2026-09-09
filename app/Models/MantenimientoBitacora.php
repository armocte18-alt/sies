<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['mantenimiento_id', 'usuario_id', 'accion', 'comentario', 'valor_anterior', 'valor_nuevo'])]
class MantenimientoBitacora extends Model
{
    use HasFactory;

    protected $table = 'mantenimiento_bitacora';

    public function mantenimiento(): BelongsTo
    {
        return $this->belongsTo(Mantenimiento::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
