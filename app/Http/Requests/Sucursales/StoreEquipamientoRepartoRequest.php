<?php

namespace App\Http\Requests\Sucursales;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreEquipamientoRepartoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateReparto', $this->route('sucursal'));
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', 'string', 'max:60'],
            'talla' => ['nullable', 'string', 'max:20'],
            'cantidad_total' => ['required', 'integer', 'min:0'],
            'cantidad_buen_estado' => ['required', 'integer', 'min:0', 'lte:cantidad_total'],
            'cantidad_danado' => ['required', 'integer', 'min:0', 'lte:cantidad_total'],
            'notas' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $talla = $this->input('talla') ? mb_strtolower(trim((string) $this->input('talla')), 'UTF-8') : null;

            $existe = DB::table('sucursal_equipamiento_repartos')
                ->where('sucursal_id', $this->route('sucursal')->id)
                ->whereRaw('LOWER(tipo) = ?', [mb_strtolower(trim((string) $this->input('tipo')), 'UTF-8')])
                ->when($talla === null, fn ($query) => $query->whereNull('talla'))
                ->when($talla !== null, fn ($query) => $query->whereRaw('LOWER(talla) = ?', [$talla]))
                ->exists();

            if ($existe) {
                $validator->errors()->add('tipo', 'Ya existe un registro de este tipo de equipo con esa talla; edítalo en la tabla en vez de agregarlo de nuevo.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'cantidad_buen_estado' => 'cantidad en buen estado',
            'cantidad_danado' => 'cantidad dañada',
        ];
    }
}
