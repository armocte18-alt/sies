<?php

namespace App\Http\Requests\Tarjetas;

use Illuminate\Foundation\Http\FormRequest;

class RetirarPorSucursalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('tarjetas.gestionar');
    }

    public function rules(): array
    {
        return [
            'destino_sucursal_id' => ['required', 'exists:sucursales,id'],
            'motivo' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return ['destino_sucursal_id' => 'sucursal'];
    }
}
