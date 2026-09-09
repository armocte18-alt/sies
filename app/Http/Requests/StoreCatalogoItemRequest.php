<?php

namespace App\Http\Requests;

use App\Support\CatalogoRhRegistro;
use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogoItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('catalogos-rh.gestionar');
    }

    /**
     * Lowercase before validating, so the uniqueness check doesn't depend on
     * the database collation being case-insensitive (it isn't in SQLite).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => $this->filled('nombre') ? mb_strtolower(trim($this->string('nombre'))) : $this->input('nombre'),
            'descripcion' => $this->filled('descripcion') ? mb_strtolower(trim($this->string('descripcion'))) : $this->input('descripcion'),
        ]);
    }

    public function rules(): array
    {
        $tabla = CatalogoRhRegistro::tabla($this->route('catalogo'));

        return [
            'nombre' => ['required', 'string', 'max:150', "unique:{$tabla},nombre"],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'orden' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'descripcion' => 'descripción',
            'orden' => 'orden',
        ];
    }
}
