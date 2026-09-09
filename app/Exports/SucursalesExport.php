<?php

namespace App\Exports;

use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SucursalesExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private readonly Builder $query) {}

    public function collection(): Collection
    {
        return $this->query->get();
    }

    public function headings(): array
    {
        return ['Registro', 'Nombre oficial', 'Alcaldía', 'Domicilio completo', 'Entre calles', 'Referencia', 'Horario', 'Horario de guardia', 'Titular', 'Estatus'];
    }

    /**
     * @param  Sucursal  $sucursal
     */
    public function map($sucursal): array
    {
        return [
            $sucursal->clave_financiera,
            $sucursal->nombre_oficial,
            $sucursal->ubicacion?->alcaldia?->nombre ?? '',
            $sucursal->ubicacion?->domicilioCompleto() ?? '',
            $sucursal->ubicacion?->entreCalles() ?? '',
            $sucursal->ubicacion?->referencia_visual ?? '',
            $sucursal->operacion?->resumenHorario() ?? '',
            $sucursal->operacion?->resumenGuardia() ?? '',
            $sucursal->titular?->nombre_completo ?? 'Sin encargado',
            ucfirst($sucursal->estatus_operativo),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
