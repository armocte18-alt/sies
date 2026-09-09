<?php

namespace Database\Seeders\Legacy\Concerns;

use Illuminate\Support\Str;

trait FormateaClaveSucursal
{
    /**
     * El legacy guarda las ventanillas remotas como "vr09010"; se muestran
     * como "VR-09010" en toda la UI, según lo pedido para identificar de
     * un vistazo el número de registro de cada sucursal.
     */
    protected function claveSucursal(string $registro): string
    {
        $registro = trim($registro);

        if (Str::startsWith(Str::lower($registro), 'vr')) {
            return 'VR-'.preg_replace('/\D/', '', $registro);
        }

        return strtoupper($registro);
    }
}
