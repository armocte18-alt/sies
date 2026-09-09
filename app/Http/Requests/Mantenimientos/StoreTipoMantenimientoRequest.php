<?php

namespace App\Http\Requests\Mantenimientos;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoMantenimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('mantenimientos.gestionar');
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100', 'unique:tipos_mantenimiento,nombre'],
            'color' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
        ];
    }
}
