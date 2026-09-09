<?php

namespace App\Http\Requests\Vehiculos;

use Illuminate\Foundation\Http\FormRequest;

class RechazarSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('vehiculos.gestionar');
    }

    public function rules(): array
    {
        return [
            'motivo_rechazo' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'motivo_rechazo' => 'motivo de rechazo',
        ];
    }
}
