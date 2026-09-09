<?php

namespace App\Http\Requests\Sucursales;

use Illuminate\Foundation\Http\FormRequest;

class StoreCombustibleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateReparto', $this->route('sucursal'));
    }

    public function rules(): array
    {
        return [
            'fecha' => ['required', 'date'],
            'litros' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'monto' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'kilometraje' => ['nullable', 'integer', 'min:0'],
            'notas' => ['nullable', 'string', 'max:500'],
        ];
    }
}
