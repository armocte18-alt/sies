<?php

namespace App\Http\Requests\Vehiculos;

use Illuminate\Foundation\Http\FormRequest;

class DevolucionSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('vehiculos.gestionar');
    }

    public function rules(): array
    {
        return [
            'km_final' => ['required', 'integer', 'min:0'],
            'combustible_recepcion' => ['required', 'integer', 'min:0', 'max:100'],
            'carroceria_notas_nuevas' => ['nullable', 'string', 'max:1000'],
            'checklist_gato' => ['sometimes', 'boolean'],
            'checklist_llave_cruz' => ['sometimes', 'boolean'],
            'checklist_reflejantes' => ['sometimes', 'boolean'],
            'checklist_extintor' => ['sometimes', 'boolean'],
            'observaciones_fallas' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'km_final' => 'kilometraje final',
            'combustible_recepcion' => 'combustible al recibir',
            'carroceria_notas_nuevas' => 'notas de carrocería',
            'observaciones_fallas' => 'observaciones de fallas',
        ];
    }
}
