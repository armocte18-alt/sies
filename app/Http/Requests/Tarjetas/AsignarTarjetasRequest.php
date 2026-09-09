<?php

namespace App\Http\Requests\Tarjetas;

use Illuminate\Foundation\Http\FormRequest;

class AsignarTarjetasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('tarjetas.gestionar');
    }

    public function rules(): array
    {
        return [
            'producto_id' => ['required', 'exists:tarjetas_productos,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'destino_sucursal_id' => ['required_without:otro_destino_descripcion', 'nullable', 'exists:sucursales,id'],
            'otro_destino_descripcion' => ['required_without:destino_sucursal_id', 'nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'producto_id' => 'producto',
            'destino_sucursal_id' => 'sucursal destino',
            'otro_destino_descripcion' => 'descripción del destino',
        ];
    }
}
