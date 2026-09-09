<?php

namespace App\Http\Requests\Sucursales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateActivoTiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateEquipamiento', $this->route('sucursal'));
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', Rule::in(['computadora', 'impresora', 'servidor', 'camara', 'switch', 'router', 'otro'])],
            'etiqueta_inventario' => ['nullable', 'string', 'max:60'],
            'marca' => ['nullable', 'string', 'max:60'],
            'modelo' => ['nullable', 'string', 'max:60'],
            'numero_serie' => ['nullable', 'string', 'max:60'],
            'estado' => ['required', Rule::in(['operativo', 'mantenimiento', 'baja'])],
            'fecha_asignacion' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'etiqueta_inventario' => 'número de inventario',
            'numero_serie' => 'número de serie',
            'fecha_asignacion' => 'fecha de asignación',
        ];
    }
}
