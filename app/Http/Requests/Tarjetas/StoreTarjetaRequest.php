<?php

namespace App\Http\Requests\Tarjetas;

use Illuminate\Foundation\Http\FormRequest;

class StoreTarjetaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('tarjetas.gestionar');
    }

    public function rules(): array
    {
        return [
            'producto_id' => ['required', 'exists:tarjetas_productos,id'],
            'numero_tarjeta' => ['required', 'digits:4'],
            'cuenta' => ['required', 'string', 'max:255', 'unique:tarjetas_inventario,cuenta'],
            'fecha_recepcion' => ['required', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'producto_id' => 'producto',
            'numero_tarjeta' => 'número de tarjeta',
            'fecha_recepcion' => 'fecha de recepción',
        ];
    }
}
