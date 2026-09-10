<?php

namespace App\Http\Requests\Sucursales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInmuebleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateInmueble', $this->route('sucursal'));
    }

    public function rules(): array
    {
        return [
            'tipo_inmueble' => ['nullable', Rule::in(['propio', 'arrendado', 'comodato', 'otro'])],
            'tipo_contrato_posesion' => ['nullable', Rule::in(['propio', 'arrendado', 'comodato', 'otro'])],
            'superficie_m2' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'medidas' => ['nullable', 'string', 'max:100'],
            'fecha_inicio_contrato' => ['nullable', 'date'],
            'fecha_fin_contrato' => ['nullable', 'date', 'after_or_equal:fecha_inicio_contrato'],
            'monto_renta_mensual' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'propietario_arrendador' => ['nullable', 'string', 'max:150'],
            'numero_escritura_contrato' => ['nullable', 'string', 'max:60'],
            'cuenta_proteccion_civil' => ['sometimes', 'boolean'],
            'numero_dictamen_proteccion_civil' => ['nullable', 'string', 'max:60'],
            'vigencia_proteccion_civil' => ['nullable', 'date'],
            'tipo_caja_fuerte' => ['nullable', Rule::in(['disco', 'llave'])],
            'modelo_caja_fuerte' => ['nullable', 'string', 'max:100'],
            'numero_inventario_caja_fuerte' => ['nullable', 'string', 'max:60'],
            'caja_fuerte_tiene_llave' => ['sometimes', 'boolean'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tipo_inmueble' => 'tipo de inmueble',
            'tipo_contrato_posesion' => 'tipo de contrato de posesión',
            'superficie_m2' => 'superficie (m²)',
            'fecha_inicio_contrato' => 'fecha de inicio de contrato',
            'fecha_fin_contrato' => 'fecha de fin de contrato',
            'monto_renta_mensual' => 'monto de renta mensual',
            'propietario_arrendador' => 'propietario / arrendador',
            'numero_escritura_contrato' => 'número de escritura o contrato',
            'numero_dictamen_proteccion_civil' => 'número de dictamen de protección civil',
            'vigencia_proteccion_civil' => 'vigencia de protección civil',
            'tipo_caja_fuerte' => 'tipo de caja fuerte',
            'modelo_caja_fuerte' => 'modelo de caja fuerte',
            'numero_inventario_caja_fuerte' => 'número de inventario de la caja fuerte',
            'caja_fuerte_tiene_llave' => 'cuenta con llave',
        ];
    }
}
