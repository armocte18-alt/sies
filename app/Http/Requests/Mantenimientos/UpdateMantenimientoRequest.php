<?php

namespace App\Http\Requests\Mantenimientos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMantenimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('mantenimientos.gestionar');
    }

    public function rules(): array
    {
        return [
            'sucursal_id' => ['nullable', 'exists:sucursales,id'],
            'tipo_mantenimiento_id' => ['required', 'exists:tipos_mantenimiento,id'],
            'titulo' => ['required', 'string', 'max:200'],
            'descripcion' => ['nullable', 'string'],
            'prioridad' => ['required', Rule::in(['baja', 'media', 'alta', 'urgente'])],
            'fecha_programada_inicio' => ['required', 'date'],
            'fecha_programada_fin' => ['required', 'date', 'after_or_equal:fecha_programada_inicio'],
            'costo_mano_obra' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'sucursal_id' => 'sucursal',
            'tipo_mantenimiento_id' => 'tipo de mantenimiento',
            'fecha_programada_inicio' => 'fecha programada de inicio',
            'fecha_programada_fin' => 'fecha programada de fin',
            'costo_mano_obra' => 'costo de mano de obra',
        ];
    }
}
