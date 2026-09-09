<?php

namespace App\Http\Requests\Sucursales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIdentificacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateIdentificacion', $this->route('sucursal'));
    }

    public function rules(): array
    {
        $sucursal = $this->route('sucursal');

        return [
            'nombre_oficial' => ['required', 'string', 'max:150'],
            'clave_financiera' => [
                'required', 'string', 'max:20',
                Rule::unique('sucursales', 'clave_financiera')->ignore($sucursal->id),
            ],
            'centro_distribucion' => ['nullable', 'string', 'max:20'],
            'estatus_operativo' => ['required', Rule::in(['activa', 'inactiva', 'suspendida', 'en_apertura'])],
            'titular_empleado_id' => ['nullable', 'integer', 'exists:empleados,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre_oficial' => 'nombre oficial',
            'clave_financiera' => 'clave financiera',
            'centro_distribucion' => 'centro de distribución',
            'estatus_operativo' => 'estatus operativo',
            'titular_empleado_id' => 'titular / jefe',
        ];
    }
}
