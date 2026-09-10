<?php

namespace App\Http\Requests\Sucursales;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoDocumentoAcervoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('sucursales.editar.acervo');
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100', 'unique:tipos_documento_acervo,nombre'],
        ];
    }

    public function attributes(): array
    {
        return ['nombre' => 'nombre'];
    }
}
