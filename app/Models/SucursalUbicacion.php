<?php

namespace App\Models;

use App\Models\Concerns\HasNormalizedCase;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'sucursal_id', 'calle', 'num_ext', 'num_int', 'colonia', 'alcaldia_id',
    'codigo_postal', 'entre_calle_1', 'entre_calle_2', 'referencia_visual',
    'latitud', 'longitud', 'clave_geografica_inegi',
])]
class SucursalUbicacion extends Model
{
    use HasFactory, HasNormalizedCase;

    protected $table = 'sucursal_ubicaciones';

    protected function casts(): array
    {
        return [
            'latitud' => 'decimal:6',
            'longitud' => 'decimal:6',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function alcaldia(): BelongsTo
    {
        return $this->belongsTo(Alcaldia::class);
    }

    public function domicilioCompleto(): string
    {
        $calle = trim("{$this->calle} {$this->num_ext}");

        return collect([
            $this->num_int ? "{$calle} Int. {$this->num_int}" : $calle,
            $this->colonia ? "Col. {$this->colonia}" : null,
            $this->codigo_postal ? "C.P. {$this->codigo_postal}" : null,
        ])->filter()->implode(', ');
    }

    public function entreCalles(): ?string
    {
        if ($this->entre_calle_1 && $this->entre_calle_2) {
            return "Entre {$this->entre_calle_1} y {$this->entre_calle_2}";
        }

        return $this->entre_calle_1 ? "Entre {$this->entre_calle_1}" : null;
    }

    public function urlMapa(): ?string
    {
        if (! $this->latitud || ! $this->longitud) {
            return null;
        }

        return "https://www.google.com/maps?q={$this->latitud},{$this->longitud}";
    }
}
