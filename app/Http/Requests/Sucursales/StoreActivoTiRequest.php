<?php

namespace App\Http\Requests\Sucursales;

use App\Models\ActivoTi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivoTiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateEquipamiento', $this->route('sucursal'));
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', Rule::in(array_keys(ActivoTi::TIPOS))],
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
