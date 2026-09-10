<?php

namespace App\Http\Requests\Sucursales;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFinanzasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateFinanzas', $this->route('sucursal'));
    }

    public function rules(): array
    {
        return [
            'limite_existencia_caja' => ['required', 'numeric', 'min:0', 'max:999999999999.99'],
        ];
    }

    public function attributes(): array
    {
        return [
            'limite_existencia_caja' => 'límite de existencia en caja',
        ];
    }
}
