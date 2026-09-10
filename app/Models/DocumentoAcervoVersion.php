<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['documento_id', 'ruta_archivo', 'nombre_original', 'hash_sha256', 'numero_version', 'comentario_version', 'subido_por'])]
class DocumentoAcervoVersion extends Model
{
    use HasFactory;

    protected $table = 'documento_acervo_versiones';

    public function documento(): BelongsTo
    {
        return $this->belongsTo(DocumentoAcervo::class, 'documento_id');
    }

    public function subidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por');
    }
}
