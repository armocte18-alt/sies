<?php

namespace App\Http\Requests\Sucursales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMotocicletaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateReparto', $this->route('sucursal'));
    }

    public function rules(): array
    {
        return [
            'placa' => ['nullable', 'string', 'max:20'],
            'marca' => ['nullable', 'string', 'max:60'],
            'modelo' => ['nullable', 'string', 'max:60'],
            'anio' => ['nullable', 'integer', 'min:1980', 'max:'.(date('Y') + 1)],
            'kilometraje_actual' => ['required', 'integer', 'min:0'],
            'estado' => ['required', Rule::in(['operativa', 'mantenimiento', 'baja'])],
            'fecha_alta' => ['nullable', 'date'],
            'notas' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'kilometraje_actual' => 'kilometraje',
            'fecha_alta' => 'fecha de alta',
        ];
    }
}
