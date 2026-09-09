<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('accesos.gestionar');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-z0-9-]*(\.[a-z][a-z0-9-]*)+$/', 'unique:permissions,name'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'nombre'];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'El nombre debe seguir el formato "modulo.accion" (por ejemplo: "logistica.ver" o "logistica.gestionar"), en minúsculas.',
        ];
    }
}
