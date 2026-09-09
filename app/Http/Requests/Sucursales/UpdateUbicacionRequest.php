<?php

namespace App\Http\Requests\Sucursales;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUbicacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateUbicacion', $this->route('sucursal'));
    }

    public function rules(): array
    {
        return [
            'calle' => ['required', 'string', 'max:150'],
            'num_ext' => ['nullable', 'string', 'max:20'],
            'num_int' => ['nullable', 'string', 'max:20'],
            'colonia' => ['required', 'string', 'max:150'],
            'alcaldia_id' => ['required', 'integer', 'exists:alcaldias,id'],
            'codigo_postal' => ['required', 'digits:5'],
            'entre_calle_1' => ['nullable', 'string', 'max:150'],
            'entre_calle_2' => ['nullable', 'string', 'max:150'],
            'referencia_visual' => ['nullable', 'string', 'max:255'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'clave_geografica_inegi' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function attributes(): array
    {
        return [
            'alcaldia_id' => 'alcaldía / municipio',
            'codigo_postal' => 'código postal',
            'entre_calle_1' => 'entre calle 1',
            'entre_calle_2' => 'entre calle 2',
            'referencia_visual' => 'referencia visual',
            'clave_geografica_inegi' => 'clave geográfica INEGI',
        ];
    }
}
