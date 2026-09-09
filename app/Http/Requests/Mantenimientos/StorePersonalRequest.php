<?php

namespace App\Http\Requests\Mantenimientos;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('mantenimientos.gestionar');
    }

    public function rules(): array
    {
        return [
            'empleado_id' => ['required', 'exists:empleados,id'],
            'rol_en_mantenimiento' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'empleado_id' => 'empleado',
            'rol_en_mantenimiento' => 'rol',
        ];
    }
}
