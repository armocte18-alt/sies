<?php

namespace App\Http\Requests\Circulares;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCircularRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('circulares.gestionar');
    }

    public function rules(): array
    {
        return [
            'numero' => ['required', 'string', 'max:30', Rule::unique('circulares', 'numero')->ignore($this->route('circular'))],
            'asunto' => ['required', 'string', 'max:10000'],
            'fecha_aplicacion' => ['nullable', 'date'],
            'ambito' => ['nullable', 'string', 'max:60'],
            'enlace_externo' => ['nullable', 'url', 'max:500'],
            'archivo' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'tips' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['numero' => trim((string) $this->input('numero'))]);
    }

    public function attributes(): array
    {
        return [
            'numero' => 'número de circular',
            'fecha_aplicacion' => 'fecha de aplicación',
            'enlace_externo' => 'enlace',
            'archivo' => 'archivo PDF',
        ];
    }
}
