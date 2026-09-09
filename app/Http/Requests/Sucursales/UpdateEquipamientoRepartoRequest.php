<?php

namespace App\Http\Requests\Sucursales;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEquipamientoRepartoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateReparto', $this->route('sucursal'));
    }

    public function rules(): array
    {
        return [
            'cantidad_total' => ['required', 'integer', 'min:0'],
            'cantidad_buen_estado' => ['required', 'integer', 'min:0', 'lte:cantidad_total'],
            'cantidad_danado' => ['required', 'integer', 'min:0', 'lte:cantidad_total'],
            'notas' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'cantidad_buen_estado' => 'cantidad en buen estado',
            'cantidad_danado' => 'cantidad dañada',
        ];
    }
}
