<?php

namespace App\Http\Requests\Calendario;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventoRapidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('calendario.gestionar');
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ];
    }
}
