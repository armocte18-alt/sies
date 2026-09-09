<?php

namespace App\Http\Requests\Mantenimientos;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('mantenimientos.gestionar');
    }

    public function rules(): array
    {
        return [
            'nombre_material' => ['required', 'string', 'max:150'],
            'unidad' => ['nullable', 'string', 'max:30'],
            'cantidad' => ['required', 'numeric', 'min:0.01'],
            'costo_unitario' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre_material' => 'material',
            'costo_unitario' => 'costo unitario',
        ];
    }
}
