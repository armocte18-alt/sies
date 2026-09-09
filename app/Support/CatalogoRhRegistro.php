<?php

namespace App\Support;

use App\Models\Especialidad;
use App\Models\Labor;
use App\Models\NivelEducativo;
use App\Models\Puesto;
use App\Models\TipoControlAsistencia;
use App\Models\TipoNombramiento;

/**
 * Registry of the simple "nombre" catalogs managed from Ajustes RR.HH., so
 * the controller and the form requests share one source of truth for which
 * route segment maps to which model/table.
 */
class CatalogoRhRegistro
{
    /** @var array<string, class-string> */
    public const MODELOS = [
        'nombramientos' => TipoNombramiento::class,
        'asistencia' => TipoControlAsistencia::class,
        'escolaridad' => NivelEducativo::class,
        'especialidades' => Especialidad::class,
        'puestos' => Puesto::class,
        'labores' => Labor::class,
    ];

    public static function modelo(string $catalogo): string
    {
        return self::MODELOS[$catalogo] ?? abort(404);
    }

    public static function tabla(string $catalogo): string
    {
        return (new (self::modelo($catalogo)))->getTable();
    }

    public static function patronRuta(): string
    {
        return implode('|', array_keys(self::MODELOS));
    }
}
