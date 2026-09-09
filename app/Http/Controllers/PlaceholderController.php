<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Sirve una vista "en construcción" para módulos del menú que aún no se
 * han implementado. El acceso ya está protegido por el middleware `can:`
 * en routes/web.php con el permiso real que tendrá cada módulo.
 */
class PlaceholderController extends Controller
{
    public function __invoke(string $titulo, string $descripcion): View
    {
        return view('modules.placeholder', [
            'titulo' => $titulo,
            'descripcion' => $descripcion,
        ]);
    }
}
