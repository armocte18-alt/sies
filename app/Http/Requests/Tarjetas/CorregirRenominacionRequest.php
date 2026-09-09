<?php

namespace App\Http\Requests\Tarjetas;

use Illuminate\Foundation\Http\FormRequest;

class CorregirRenominacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('tarjetas.gestionar');
    }

    public function rules(): array
    {
        return [
            'motivo' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }
}
