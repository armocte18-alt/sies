<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

/**
 * Genera el .xlsx a partir de una vista Blade (una sola <table> con el
 * membrete institucional y el mismo desglose de domicilio/horario en
 * varias filas por celdas combinadas) para que se vea igual que el PDF
 * y que el "directorio" impreso/copiado desde sios-app-web.
 */
class SucursalesExport implements FromView, ShouldAutoSize
{
    public function __construct(
        private readonly Collection $sucursales,
        private readonly string $filtrosResumen,
    ) {}

    public function view(): View
    {
        return view('sucursales.exports.listado', [
            'sucursales' => $this->sucursales,
            'filtrosResumen' => $this->filtrosResumen,
        ]);
    }
}
