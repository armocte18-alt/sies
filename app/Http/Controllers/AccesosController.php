<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserPermissionsRequest;
use App\Http\Requests\UpdateUserRolesRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccesosController extends Controller
{
    public function index(Request $request): View
    {
        $usuarios = User::query()
            ->with(['coordinacion', 'roles'])
            ->orderBy('name')
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $buscar = $request->string('buscar');
                $query->where(function ($q) use ($buscar) {
                    $q->where('name', 'like', "%{$buscar}%")
                        ->orWhere('email', 'like', "%{$buscar}%");
                });
            })
            ->when($request->filled('rol'), fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', $request->string('rol'))))
            ->paginate(15)
            ->withQueryString();

        return view('accesos.index', [
            'usuarios' => $usuarios,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function roles(): View
    {
        return view('accesos.roles', [
            'roles' => Role::with('permissions')->orderBy('name')->get(),
            'permisos' => Permission::orderBy('name')->get(),
        ]);
    }

    public function edit(User $user): View
    {
        $user->load('roles', 'permissions');

        $permisosPorModulo = Permission::orderBy('name')->get()->groupBy(
            fn (Permission $permiso) => explode('.', $permiso->name)[0]
        );

        return view('accesos.edit', [
            'usuario' => $user,
            'roles' => Role::orderBy('name')->get(),
            'permisosPorModulo' => $permisosPorModulo,
            'esUnoMismo' => $user->id === auth()->id(),
        ]);
    }

    public function updateRoles(UpdateUserRolesRequest $request, User $user): RedirectResponse
    {
        $rolesNuevos = $request->validated('roles', []);

        if ($this->romperiaElUltimoAdministrador($user, $rolesNuevos)) {
            return back()->with('error', 'No puedes quitar el rol de administrador al último administrador activo del sistema.');
        }

        $user->syncRoles($rolesNuevos);

        return back()->with('status', 'Roles de ' . $user->name . ' actualizados.');
    }

    public function updatePermissions(UpdateUserPermissionsRequest $request, User $user): RedirectResponse
    {
        $user->syncPermissions($request->validated('permisos', []));

        return back()->with('status', 'Permisos especiales de ' . $user->name . ' actualizados.');
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->can('accesos.gestionar'), 403);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        if ($user->activo && $this->esUltimoAdministradorActivo($user)) {
            return back()->with('error', 'No puedes desactivar al último administrador activo del sistema.');
        }

        $user->update(['activo' => ! $user->activo]);

        return back()->with('status', $user->activo
            ? $user->name . ' fue reactivado.'
            : $user->name . ' fue desactivado.');
    }

    /**
     * Evita dejar el sistema sin ningún administrador activo al remover ese
     * rol del único usuario que lo tiene.
     */
    private function romperiaElUltimoAdministrador(User $user, array $rolesNuevos): bool
    {
        if (! $user->hasRole('administrador') || in_array('administrador', $rolesNuevos, true)) {
            return false;
        }

        return $this->esUltimoAdministradorActivo($user);
    }

    private function esUltimoAdministradorActivo(User $user): bool
    {
        if (! $user->hasRole('administrador')) {
            return false;
        }

        return User::role('administrador')
            ->where('activo', true)
            ->where('id', '!=', $user->id)
            ->doesntExist();
    }
}
