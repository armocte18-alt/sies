<?php

namespace App\Http\Requests\Sucursales;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEquipamientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateEquipamiento', $this->route('sucursal'));
    }

    public function rules(): array
    {
        return [
            'num_computadoras' => ['required', 'integer', 'min:0', 'max:9999'],
            'num_impresoras' => ['required', 'integer', 'min:0', 'max:9999'],
            'num_servidores' => ['required', 'integer', 'min:0', 'max:9999'],
            'tiene_camaras' => ['required', 'boolean'],
            'num_camaras' => ['required', 'integer', 'min:0', 'max:9999'],
            'tiene_alarma' => ['required', 'boolean'],
            'tiene_extintores' => ['required', 'boolean'],
            'num_extintores' => ['required', 'integer', 'min:0', 'max:9999'],
            'proveedor_internet' => ['nullable', 'string', 'max:100'],
            'tipo_enlace' => ['nullable', 'string', 'max:100'],
            'velocidad_contratada' => ['nullable', 'string', 'max:50'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'num_computadoras' => 'número de computadoras',
            'num_impresoras' => 'número de impresoras',
            'num_servidores' => 'número de servidores',
            'tiene_camaras' => 'cuenta con cámaras',
            'num_camaras' => 'número de cámaras',
            'tiene_alarma' => 'cuenta con alarma',
            'tiene_extintores' => 'cuenta con extintores',
            'num_extintores' => 'número de extintores',
            'proveedor_internet' => 'proveedor de internet',
            'tipo_enlace' => 'tipo de enlace',
            'velocidad_contratada' => 'velocidad contratada',
        ];
    }
}
