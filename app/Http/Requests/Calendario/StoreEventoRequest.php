<?php

namespace App\Http\Requests\Calendario;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('calendario.gestionar');
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'hora_inicio' => ['nullable', 'date_format:H:i'],
            'hora_fin' => ['nullable', 'date_format:H:i', 'after:hora_inicio'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'tipo_asociado' => ['nullable', 'in:area_central,gerencia,sucursal,externo'],
            'asociado_nombre' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'invitados' => ['nullable', 'string', 'max:1000'],
            'notas' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'hora_inicio' => 'hora de inicio',
            'hora_fin' => 'hora de fin',
            'tipo_asociado' => 'tipo de asociado',
            'asociado_nombre' => 'nombre del asociado',
        ];
    }
}
