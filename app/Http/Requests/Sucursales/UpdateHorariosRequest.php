<?php

namespace App\Http\Requests\Sucursales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHorariosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateHorarios', $this->route('sucursal'));
    }

    public function rules(): array
    {
        return [
            'dias_laborables' => ['nullable', 'string', 'max:60'],
            'hora_apertura_publico' => ['nullable', 'date_format:H:i'],
            'hora_cierre_publico' => ['nullable', 'date_format:H:i', 'after:hora_apertura_publico'],
            'hora_inicio_labores_interno' => ['nullable', 'date_format:H:i'],
            'hora_fin_labores_interno' => ['nullable', 'date_format:H:i', 'after:hora_inicio_labores_interno'],
            'tipo_poblacion' => ['nullable', Rule::in([
                'urbana_alta_densidad', 'urbana_media_densidad', 'urbana_baja_densidad', 'rural',
            ])],
            'telefono' => ['nullable', 'string', 'max:20'],
            'reparto_activo' => ['required', 'boolean'],
            'enrutamiento' => ['nullable', 'string', 'max:60'],
            'dias_guardia' => ['nullable', 'string', 'max:60'],
            'apertura_guardia' => ['nullable', 'date_format:H:i'],
            'cierre_guardia' => ['nullable', 'date_format:H:i', 'after:apertura_guardia'],
        ];
    }

    public function attributes(): array
    {
        return [
            'dias_laborables' => 'días laborables',
            'hora_apertura_publico' => 'hora de apertura (público)',
            'hora_cierre_publico' => 'hora de cierre (público)',
            'hora_inicio_labores_interno' => 'hora inicio de labores (interno)',
            'hora_fin_labores_interno' => 'hora fin de labores (interno)',
            'tipo_poblacion' => 'tipo de población',
            'reparto_activo' => 'reparto activo',
            'dias_guardia' => 'días de guardia',
            'apertura_guardia' => 'apertura de guardia',
            'cierre_guardia' => 'cierre de guardia',
        ];
    }
}
