<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePersonalExternoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('directorios.gestionar');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'telefonos' => array_values(array_filter((array) $this->input('telefonos', []))),
            'correos' => array_values(array_filter((array) $this->input('correos', []))),
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'puesto' => ['nullable', 'string', 'max:150'],
            'dependencia' => ['required', 'string', 'max:150'],
            'adscripcion' => ['nullable', 'string', 'max:150'],
            'domicilio' => ['nullable', 'string', 'max:255'],
            'telefonos' => ['nullable', 'array'],
            'telefonos.*' => ['string', 'max:20'],
            'correos' => ['nullable', 'array'],
            'correos.*' => ['email', 'max:255'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }
}
