<?php

namespace App\Http\Requests\Tarjetas;

use Illuminate\Foundation\Http\FormRequest;

class RetirarTarjetaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('tarjetas.gestionar');
    }

    public function rules(): array
    {
        return [
            'motivo' => ['nullable', 'string', 'max:500'],
        ];
    }
}
