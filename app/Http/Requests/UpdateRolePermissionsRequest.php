<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRolePermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('accesos.gestionar')
            && $this->route('role')->name !== 'administrador';
    }

    public function rules(): array
    {
        return [
            'permisos' => ['sometimes', 'array'],
            'permisos.*' => ['string', 'exists:permissions,name'],
        ];
    }
}
