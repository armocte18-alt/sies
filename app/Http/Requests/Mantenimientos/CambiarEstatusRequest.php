<?php

namespace App\Http\Requests\Mantenimientos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CambiarEstatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('mantenimientos.gestionar');
    }

    public function rules(): array
    {
        return [
            'estatus' => ['required', Rule::in(['pendiente', 'programado', 'en_proceso', 'completado', 'cancelado'])],
            'comentario' => ['nullable', 'string', 'max:500'],
        ];
    }
}
