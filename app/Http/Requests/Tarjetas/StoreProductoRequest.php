<?php

namespace App\Http\Requests\Tarjetas;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('tarjetas.gestionar');
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', 'unique:tarjetas_productos,nombre'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ];
    }
}
