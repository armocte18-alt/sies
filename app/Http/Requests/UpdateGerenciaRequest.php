<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGerenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('directorios.gestionar');
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'coordinacion' => ['required', 'string', 'max:150'],
            'extension' => ['nullable', 'string', 'max:10'],
            'correo_finabien' => ['required', 'email', 'max:255'],
            'correo_sigitel' => ['nullable', 'email', 'max:255'],
            'comite' => ['nullable', 'string', 'max:150'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'activo' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'correo_finabien' => 'correo institucional',
            'correo_sigitel' => 'correo SIGITEL',
        ];
    }
}
