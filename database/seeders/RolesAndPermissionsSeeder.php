<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Catálogo de permisos del sistema, agrupado por módulo.
     * El módulo de Sucursales se alimenta de varias coordinaciones,
     * por lo que su edición se controla por sección.
     */
    private const PERMISSIONS = [
        'sucursales.ver',
        'sucursales.crear',
        'sucursales.eliminar',
        'sucursales.editar.identificacion', // Operación: cédula, titular, estatus
        'sucursales.editar.ubicacion',       // Operación
        'sucursales.editar.horarios',        // Operación
        'sucursales.editar.inmueble',        // Administración
        'sucursales.editar.equipamiento',    // Técnica
        'sucursales.editar.finanzas',        // Finanzas
        'sucursales.editar.reparto',         // Operación: motocicletas y EPP de mensajería
        'empleados.ver',
        'empleados.gestionar',
        'directorios.ver',
        'directorios.gestionar',
        'minutarios.ver',
        'minutarios.gestionar',
        'circulares.ver',
        'circulares.gestionar',
        'calendario.ver',
        'calendario.gestionar',
        'tarjetas.ver',
        'tarjetas.gestionar',
        'vehiculos.ver',
        'vehiculos.gestionar',
        'mantenimientos.ver',
        'mantenimientos.gestionar',
        'metas.ver',
        'metas.gestionar',
        'kardex.gestionar',
        'catalogos-rh.gestionar',
        'accesos.gestionar',
    ];

    /**
     * Coordinación (clave) => permisos asignados por defecto.
     */
    private const ROLE_PERMISSIONS = [
        'administrador' => ['*'],
        'supervision' => ['sucursales.ver', 'empleados.ver', 'metas.ver', 'calendario.ver'],
        'operacion' => [
            'sucursales.ver', 'sucursales.crear',
            'sucursales.editar.identificacion', 'sucursales.editar.ubicacion', 'sucursales.editar.horarios',
            'sucursales.editar.reparto',
            'empleados.ver', 'calendario.ver', 'calendario.gestionar',
        ],
        'administracion' => [
            'sucursales.ver', 'sucursales.editar.inmueble',
            'vehiculos.ver', 'vehiculos.gestionar', 'mantenimientos.ver', 'mantenimientos.gestionar',
        ],
        'tecnica' => ['sucursales.ver', 'sucursales.editar.equipamiento', 'mantenimientos.ver'],
        'finanzas' => ['sucursales.ver', 'sucursales.editar.finanzas', 'tarjetas.ver', 'tarjetas.gestionar'],
        'rrhh' => ['empleados.ver', 'empleados.gestionar', 'kardex.gestionar', 'catalogos-rh.gestionar', 'directorios.ver', 'directorios.gestionar'],
        'juridico' => ['sucursales.ver', 'circulares.ver', 'circulares.gestionar', 'minutarios.ver', 'minutarios.gestionar'],
        'creditos' => ['sucursales.ver', 'metas.ver'],
        'comercial' => ['sucursales.ver', 'metas.ver', 'metas.gestionar'],
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        foreach (self::ROLE_PERMISSIONS as $clave => $permissions) {
            $role = Role::firstOrCreate(['name' => $clave, 'guard_name' => 'web']);

            if ($permissions === ['*']) {
                $role->syncPermissions(Permission::where('guard_name', 'web')->get());

                continue;
            }

            $role->syncPermissions($permissions);
        }
    }
}
