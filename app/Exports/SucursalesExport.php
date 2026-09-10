<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

/**
 * Genera el .xlsx a partir de una vista Blade (una sola <table> con el
 * membrete institucional y el mismo desglose de domicilio/horario en
 * varias filas por celdas combinadas) para que se vea igual que el PDF
 * y que el "directorio" impreso/copiado desde sios-app-web.
 */
class SucursalesExport implements FromView, ShouldAutoSize, WithDrawings
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

    public function drawings(): Drawing
    {
        $logo = new Drawing();
        $logo->setName('Financiera para el Bienestar');
        $logo->setPath(public_path('images/fondo_finabien.png'));
        $logo->setHeight(45);
        $logo->setResizeProportional(true);
        $logo->setCoordinates('A1');
        $logo->setOffsetX(4);
        $logo->setOffsetY(4);

        return $logo;
    }
}
