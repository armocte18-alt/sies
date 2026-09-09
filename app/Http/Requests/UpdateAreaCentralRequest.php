<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAreaCentralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('directorios.gestionar');
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'puesto' => ['nullable', 'string', 'max:150'],
            'adscripcion' => ['required', 'string', 'max:150'],
            'gerencia_id' => ['nullable', 'exists:gerencias,id'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'extension' => ['nullable', 'string', 'max:10'],
            'correo_finabien' => ['required', 'email', 'max:255'],
            'correo_sigitel' => ['nullable', 'email', 'max:255'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'gerencia_id' => 'gerencia',
            'correo_finabien' => 'correo institucional',
            'correo_sigitel' => 'correo SIGITEL',
        ];
    }
}
