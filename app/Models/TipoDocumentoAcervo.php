<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'slug', 'activo', 'orden'])]
class TipoDocumentoAcervo extends Model
{
    use HasFactory;

    protected $table = 'tipos_documento_acervo';

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoAcervo::class, 'tipo_documento_id');
    }
}
