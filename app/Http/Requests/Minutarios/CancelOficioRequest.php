<?php

namespace App\Http\Requests\Minutarios;

use Illuminate\Foundation\Http\FormRequest;

class CancelOficioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('minutarios.gestionar');
    }

    public function rules(): array
    {
        return [
            'motivo_cancelacion' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return ['motivo_cancelacion' => 'motivo de cancelación'];
    }
}
