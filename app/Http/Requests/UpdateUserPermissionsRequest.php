<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserPermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('accesos.gestionar')
            && (int) $this->route('user')->id !== $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'permisos' => ['sometimes', 'array'],
            'permisos.*' => ['string', 'exists:permissions,name'],
        ];
    }
}
