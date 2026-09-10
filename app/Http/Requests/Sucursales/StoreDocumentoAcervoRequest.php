<?php

namespace App\Http\Requests\Sucursales;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoAcervoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateAcervo', $this->route('sucursal'));
    }

    public function rules(): array
    {
        return [
            'tipo_documento_id' => ['required', 'exists:tipos_documento_acervo,id'],
            'folio' => ['nullable', 'string', 'max:255'],
            'fecha_documento' => ['nullable', 'date'],
            'comentario_version' => ['nullable', 'string', 'max:500'],
            'archivo' => ['required', 'file', 'max:20480', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tipo_documento_id' => 'tipo de documento',
            'fecha_documento' => 'fecha del documento',
            'comentario_version' => 'comentario',
            'archivo' => 'archivo',
        ];
    }
}
