<?php

namespace Database\Seeders\Legacy;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * Clona las cuentas reales de "sios_app_web" (contraseña incluida: ambos
 * sistemas usan el hash bcrypt de Laravel, así que cada quien conserva su
 * propia contraseña) y su rol asignado, mapeado 1 a 1 a los roles de SIES.
 *
 * Nunca toca una cuenta cuyo correo ya exista en SIES (p. ej. el admin
 * inicial sembrado por AdminUserSeeder) para no pisar una contraseña que el
 * usuario ya haya establecido aquí.
 *
 * Ejecutar manualmente:
 *   php artisan db:seed --class="Database\Seeders\Legacy\MigrarUsuariosSeeder"
 */
class MigrarUsuariosSeeder extends Seeder
{
    private const RUTA_AVATARS_LEGACY = 'D:\\Servidor\\www\\sios-app-web\\storage\\app\\public\\avatars';

    private const MAPA_ROLES = [
        'super_administrador' => 'administrador',
        'administrador' => 'administrador',
        'administración' => 'administracion',
        'comercial' => 'comercial',
        'créditos' => 'creditos',
        'finanzas' => 'finanzas',
        'jurídico' => 'juridico',
        'operación' => 'operacion',
        'supervisión' => 'supervision',
        'técnica' => 'tecnica',
        // 'jefatura' e 'invitado' no tienen equivalente directo en SIES; se
        // migran sin rol y se asignan manualmente desde Control de Accesos.
    ];

    public function run(): void
    {
        $rolesPorUsuario = DB::connection('sios_legacy')->table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->pluck('roles.name', 'model_has_roles.model_id');

        $correosExistentes = User::pluck('email')->map(fn ($e) => Str::lower($e))->all();

        $creados = 0;
        $omitidos = 0;
        $conRol = 0;
        $avataresCopiados = 0;

        foreach (DB::connection('sios_legacy')->table('users')->orderBy('id')->get() as $fila) {
            if (in_array(Str::lower($fila->email), $correosExistentes, true)) {
                $omitidos++;

                continue;
            }

            $avatarPath = null;
            if ($fila->avatar) {
                $nombreArchivo = basename($fila->avatar);
                $origen = self::RUTA_AVATARS_LEGACY.DIRECTORY_SEPARATOR.$nombreArchivo;

                if (File::exists($origen)) {
                    $destino = 'avatars/'.$nombreArchivo;
                    if (! Storage::disk('public')->exists($destino)) {
                        Storage::disk('public')->put($destino, File::get($origen));
                    }
                    $avatarPath = $destino;
                    $avataresCopiados++;
                }
            }

            $usuario = User::create([
                'name' => Str::title(trim($fila->name)),
                'email' => Str::lower($fila->email),
                'password' => $fila->password,
                'avatar_path' => $avatarPath,
                'activo' => true,
            ]);

            $usuario->forceFill([
                'email_verified_at' => $fila->email_verified_at,
                'remember_token' => $fila->remember_token,
                'created_at' => $fila->created_at,
                'updated_at' => $fila->updated_at,
            ])->save();

            $creados++;

            $rolLegacy = $rolesPorUsuario[$fila->id] ?? null;
            $rolSies = $rolLegacy ? (self::MAPA_ROLES[$rolLegacy] ?? null) : null;

            if ($rolSies && Role::where('name', $rolSies)->exists()) {
                $usuario->assignRole($rolSies);
                $conRol++;
            }
        }

        $this->command?->info(sprintf(
            '%d cuentas creadas (%d con rol asignado, %d avatares copiados); %d omitidas por correo ya existente en SIES.',
            $creados,
            $conRol,
            $avataresCopiados,
            $omitidos,
        ));
    }
}
