<?php

namespace App\Http\Requests\Minutarios;

use Illuminate\Foundation\Http\FormRequest;

class StoreOficioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('minutarios.gestionar');
    }

    public function rules(): array
    {
        return [
            'asunto' => ['required', 'string'],
            'dirigido_a' => ['required', 'string', 'max:255'],
            'nombre_solicitante' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'dirigido_a' => 'dirigido a',
            'nombre_solicitante' => 'nombre del solicitante',
        ];
    }
}
