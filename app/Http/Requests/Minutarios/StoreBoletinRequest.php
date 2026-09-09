<?php

namespace App\Http\Requests\Minutarios;

use Illuminate\Foundation\Http\FormRequest;

class StoreBoletinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('minutarios.gestionar');
    }

    public function rules(): array
    {
        return [
            'asunto' => ['required', 'string', 'max:255'],
            'contenido' => ['required', 'string'],
            'fecha_vigor' => ['required', 'date'],
            'dirigido_tipo' => ['required', 'in:todos,gerencia,sucursales'],
        ];
    }

    public function attributes(): array
    {
        return [
            'fecha_vigor' => 'fecha de vigor',
            'dirigido_tipo' => 'grupo de cobertura',
        ];
    }
}
