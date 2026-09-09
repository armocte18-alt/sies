<?php

namespace App\Http\Requests\Empleados;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('empleados.gestionar');
    }

    public function rules(): array
    {
        return [
            'no_empleado' => ['required', 'string', 'max:20', Rule::unique('empleados', 'no_empleado')],
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'puesto' => ['nullable', 'string', 'max:100'],
            'funcion_laboral' => ['nullable', 'string', 'max:100'],
            'sucursal_id' => ['nullable', 'exists:sucursales,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'no_empleado' => 'número de empleado',
            'apellido_paterno' => 'apellido paterno',
            'apellido_materno' => 'apellido materno',
            'funcion_laboral' => 'función laboral',
            'sucursal_id' => 'sucursal',
        ];
    }
}
