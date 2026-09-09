<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('accesos.gestionar');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', 'regex:/^[a-z][a-z0-9-]*$/', 'unique:roles,name'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'nombre'];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'El nombre solo puede llevar minúsculas, números y guiones (por ejemplo: "logistica" o "atencion-clientes").',
        ];
    }
}
