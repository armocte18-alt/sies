<?php

namespace App\Http\Requests\Vehiculos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConductorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('vehiculos.gestionar');
    }

    public function rules(): array
    {
        return [
            'nombre_completo' => ['required', 'string', 'max:255'],
            'numero_empleado' => ['required', 'string', 'max:20'],
            'area_adscripcion' => ['nullable', 'string', 'max:255'],
            'numero_licencia' => ['required', 'string', 'max:20', 'unique:conductores,numero_licencia'],
            'tipo_licencia' => ['required', Rule::in(['A1', 'A2', 'B', 'C', 'D', 'E', 'E1', 'SICT_A', 'SICT_B', 'SICT_C', 'SICT_D', 'SICT_E', 'SICT_F'])],
            'es_permanente' => ['sometimes', 'boolean'],
            'vigencia_licencia' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre_completo' => 'nombre completo',
            'numero_empleado' => 'número de empleado',
            'area_adscripcion' => 'área de adscripción',
            'numero_licencia' => 'número de licencia',
            'tipo_licencia' => 'tipo de licencia',
            'es_permanente' => 'permanente',
            'vigencia_licencia' => 'vigencia de licencia',
        ];
    }
}
