<?php

namespace App\Policies;

use App\Models\Sucursal;
use App\Models\User;

class SucursalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('sucursales.ver');
    }

    public function view(User $user, Sucursal $sucursal): bool
    {
        return $user->can('sucursales.ver');
    }

    public function create(User $user): bool
    {
        return $user->can('sucursales.crear');
    }

    public function delete(User $user, Sucursal $sucursal): bool
    {
        return $user->can('sucursales.eliminar');
    }

    public function updateIdentificacion(User $user, Sucursal $sucursal): bool
    {
        return $user->can('sucursales.editar.identificacion');
    }

    public function updateUbicacion(User $user, Sucursal $sucursal): bool
    {
        return $user->can('sucursales.editar.ubicacion');
    }

    public function updateHorarios(User $user, Sucursal $sucursal): bool
    {
        return $user->can('sucursales.editar.horarios');
    }

    public function updateInmueble(User $user, Sucursal $sucursal): bool
    {
        return $user->can('sucursales.editar.inmueble');
    }

    public function updateEquipamiento(User $user, Sucursal $sucursal): bool
    {
        return $user->can('sucursales.editar.equipamiento');
    }

    public function updateFinanzas(User $user, Sucursal $sucursal): bool
    {
        return $user->can('sucursales.editar.finanzas');
    }

    public function updateReparto(User $user, Sucursal $sucursal): bool
    {
        return $user->can('sucursales.editar.reparto');
    }

    public function updateAcervo(User $user, Sucursal $sucursal): bool
    {
        return $user->can('sucursales.editar.acervo');
    }
}
