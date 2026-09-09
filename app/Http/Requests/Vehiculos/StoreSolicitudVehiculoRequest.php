<?php

namespace App\Http\Requests\Vehiculos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSolicitudVehiculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('vehiculos.gestionar');
    }

    public function rules(): array
    {
        return [
            'numero_empleado' => ['nullable', 'string', 'max:20'],
            'area' => ['required', Rule::in(['gerencia_estatal_cdmx', 'areas_centrales_disfo', 'otro'])],
            'area_otro' => ['required_if:area,otro', 'nullable', 'string', 'max:255'],
            'fecha_salida_desde' => ['required', 'date'],
            'fecha_salida_hasta' => ['required', 'date', 'after:fecha_salida_desde'],
            'destino_lugar' => ['required', 'string', 'max:255'],
            'motivo' => ['required', 'string', 'max:500'],
            'vehiculo_id' => ['required', 'exists:vehiculos,id'],
            'conductor_id' => ['required', 'exists:conductores,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'numero_empleado' => 'número de empleado',
            'area' => 'área',
            'area_otro' => 'especificación del área',
            'fecha_salida_desde' => 'fecha y hora de salida',
            'fecha_salida_hasta' => 'fecha y hora de regreso',
            'destino_lugar' => 'destino',
            'vehiculo_id' => 'vehículo',
            'conductor_id' => 'conductor',
        ];
    }
}
