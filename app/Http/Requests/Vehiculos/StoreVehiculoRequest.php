<?php

namespace App\Http\Requests\Vehiculos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehiculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('vehiculos.gestionar');
    }

    public function rules(): array
    {
        return [
            'marca' => ['required', 'string', 'max:255'],
            'modelo' => ['required', 'string', 'max:255'],
            'anio' => ['required', 'integer', 'min:1980', 'max:'.(now()->year + 1)],
            'tipo' => ['required', Rule::in(['propio', 'arrendado', 'otro'])],
            'tipo_otro' => ['required_if:tipo,otro', 'nullable', 'string', 'max:255'],
            'placa' => ['required', 'string', 'max:15', 'unique:vehiculos,placa'],
            'folio_tarjeta_circulacion' => ['nullable', 'string', 'max:255'],
            'vigencia_tarjeta_circulacion' => ['nullable', 'date'],
            'folio_poliza_seguro' => ['nullable', 'string', 'max:255'],
            'vigencia_poliza_seguro' => ['nullable', 'date'],
            'kilometraje_actual' => ['nullable', 'integer', 'min:0'],
            'combustible_inicial' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tipo' => 'tipo de vehículo',
            'tipo_otro' => 'especificación del tipo',
            'placa' => 'placa',
            'folio_tarjeta_circulacion' => 'folio de tarjeta de circulación',
            'vigencia_tarjeta_circulacion' => 'vigencia de tarjeta de circulación',
            'folio_poliza_seguro' => 'folio de póliza de seguro',
            'vigencia_poliza_seguro' => 'vigencia de póliza de seguro',
            'kilometraje_actual' => 'kilometraje actual',
            'combustible_inicial' => 'combustible inicial',
        ];
    }
}
