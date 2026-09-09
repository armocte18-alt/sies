<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNivelSalarialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('catalogos-rh.gestionar');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nivel_salarial' => $this->filled('nivel_salarial') ? mb_strtolower(trim($this->string('nivel_salarial'))) : $this->input('nivel_salarial'),
            'observaciones' => $this->filled('observaciones') ? mb_strtolower(trim($this->string('observaciones'))) : $this->input('observaciones'),
        ]);
    }

    public function rules(): array
    {
        return [
            'consecutivo' => [
                'required', 'integer', 'min:1',
                Rule::unique('niveles_salariales', 'consecutivo')->ignore($this->route('nivelSalarial')),
            ],
            'nivel_salarial' => [
                'required', 'string', 'max:50',
                Rule::unique('niveles_salariales', 'nivel_salarial')->ignore($this->route('nivelSalarial')),
            ],
            'sueldo_base' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'compensacion_garantizada' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'observaciones' => ['nullable', 'string', 'max:255'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'consecutivo' => 'consecutivo',
            'nivel_salarial' => 'nivel salarial',
            'sueldo_base' => 'sueldo base',
            'compensacion_garantizada' => 'compensación garantizada',
            'observaciones' => 'observaciones',
        ];
    }
}
