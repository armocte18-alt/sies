<?php

namespace App\Http\Requests\Sucursales;

use App\Models\Sucursal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSucursalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Sucursal::class);
    }

    public function rules(): array
    {
        return [
            'nombre_oficial' => ['required', 'string', 'max:150'],
            'clave_financiera' => ['required', 'string', 'max:20', 'unique:sucursales,clave_financiera'],
            'centro_distribucion' => ['nullable', 'string', 'max:20'],
            'estatus_operativo' => ['required', Rule::in(['activa', 'inactiva', 'suspendida', 'en_apertura'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre_oficial' => 'nombre oficial',
            'clave_financiera' => 'clave financiera',
            'centro_distribucion' => 'centro de distribución',
            'estatus_operativo' => 'estatus operativo',
        ];
    }
}
