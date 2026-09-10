<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['sucursal_id', 'tipo_documento_id', 'folio', 'fecha_documento', 'estatus', 'version_actual_id', 'creado_por'])]
class DocumentoAcervo extends Model
{
    use HasFactory;

    protected $table = 'documentos_acervo';

    public const ESTATUS_LABELS = [
        'vigente' => 'Vigente',
        'sustituido' => 'Sustituido',
        'baja' => 'Baja',
    ];

    protected function casts(): array
    {
        return [
            'fecha_documento' => 'date',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(TipoDocumentoAcervo::class, 'tipo_documento_id');
    }

    public function versiones(): HasMany
    {
        return $this->hasMany(DocumentoAcervoVersion::class, 'documento_id')->orderBy('numero_version');
    }

    public function versionActual(): BelongsTo
    {
        return $this->belongsTo(DocumentoAcervoVersion::class, 'version_actual_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
