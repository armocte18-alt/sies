<?php

namespace Database\Seeders\Legacy;

use App\Models\DocumentoAcervo;
use App\Models\DocumentoAcervoVersion;
use App\Models\Sucursal;
use App\Models\TipoDocumentoAcervo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Clona el acervo documental real de "sios_app_web": 19 tipos de documento,
 * 8 documentos (algunos sin archivo cargado aún, tal como en producción) y
 * 7 versiones de archivo, copiando también los PDF/imágenes físicos.
 *
 * Ejecutar manualmente:
 *   php artisan db:seed --class="Database\Seeders\Legacy\MigrarAcervoDocumentalSeeder"
 */
class MigrarAcervoDocumentalSeeder extends Seeder
{
    private const RUTA_ARCHIVOS_LEGACY = 'D:\\Servidor\\www\\sios-app-web\\storage\\app\\acervo_documental';

    private const DISCO = 'acervo_documental';

    public function run(): void
    {
        $tipoIdPorLegacy = [];
        foreach (DB::connection('sios_legacy')->table('tipos_documento_acervo')->orderBy('orden')->get() as $fila) {
            $tipo = TipoDocumentoAcervo::updateOrCreate(
                ['slug' => $fila->slug],
                ['nombre' => $fila->nombre, 'activo' => (bool) $fila->activo, 'orden' => $fila->orden],
            );
            $tipoIdPorLegacy[$fila->id] = $tipo->id;
        }

        $sucursalPorClave = Sucursal::pluck('id', 'clave_financiera');
        $sucursalPorLegacyId = DB::connection('sios_legacy')->table('sucursales')->pluck('registro_sucursal', 'id_sucursal')
            ->mapWithKeys(fn ($registro, $idLegacy) => [$idLegacy => $sucursalPorClave[str_pad($registro, 5, '0', STR_PAD_LEFT)] ?? null]);

        $correoPorUsuarioLegacy = DB::connection('sios_legacy')->table('users')->pluck('email', 'id')
            ->map(fn ($correo) => Str::lower($correo));
        $usuarioSiesPorCorreo = User::pluck('id', 'email')->mapWithKeys(fn ($id, $correo) => [Str::lower($correo) => $id]);
        $resolverUsuario = function (?int $idLegacy) use ($correoPorUsuarioLegacy, $usuarioSiesPorCorreo): ?int {
            if (! $idLegacy || ! isset($correoPorUsuarioLegacy[$idLegacy])) {
                return null;
            }

            return $usuarioSiesPorCorreo[$correoPorUsuarioLegacy[$idLegacy]] ?? null;
        };

        $documentoIdPorLegacy = [];
        $documentosCreados = 0;
        $versionActualLegacyPorDocumentoLegacy = [];
        foreach (DB::connection('sios_legacy')->table('documentos_acervo')->orderBy('id')->get() as $fila) {
            $sucursalId = $sucursalPorLegacyId[$fila->sucursal_id] ?? null;
            $tipoId = $tipoIdPorLegacy[$fila->tipo_documento_id] ?? null;

            if (! $sucursalId || ! $tipoId) {
                continue;
            }

            $documento = DocumentoAcervo::updateOrCreate(
                ['sucursal_id' => $sucursalId, 'tipo_documento_id' => $tipoId],
                [
                    'folio' => $fila->folio,
                    'fecha_documento' => $fila->fecha_documento,
                    'estatus' => $fila->estatus,
                    'creado_por' => $resolverUsuario($fila->creado_por),
                ],
            );
            $documento->forceFill(['created_at' => $fila->created_at, 'updated_at' => $fila->updated_at])->save();

            $documentoIdPorLegacy[$fila->id] = $documento->id;
            $versionActualLegacyPorDocumentoLegacy[$fila->id] = $fila->version_actual_id;
            $documentosCreados++;
        }

        $versionesCreadas = 0;
        $archivosCopiados = 0;
        $archivosFaltantes = 0;
        $nuevaVersionIdPorLegacy = [];
        foreach (DB::connection('sios_legacy')->table('documento_acervo_versiones')->orderBy('id')->get() as $fila) {
            $documentoId = $documentoIdPorLegacy[$fila->documento_id] ?? null;

            if (! $documentoId) {
                continue;
            }

            $version = DocumentoAcervoVersion::where('documento_id', $documentoId)
                ->where('numero_version', $fila->numero_version)
                ->first();

            if ($version) {
                $nuevaVersionIdPorLegacy[$fila->id] = $version->id;

                continue;
            }

            $origen = self::RUTA_ARCHIVOS_LEGACY.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $fila->ruta_archivo);
            $rutaDestino = $fila->ruta_archivo;

            if (File::exists($origen)) {
                if (! Storage::disk(self::DISCO)->exists($rutaDestino)) {
                    Storage::disk(self::DISCO)->put($rutaDestino, File::get($origen));
                }
                $archivosCopiados++;
            } else {
                $archivosFaltantes++;
            }

            $version = DocumentoAcervoVersion::create([
                'documento_id' => $documentoId,
                'ruta_archivo' => $rutaDestino,
                'nombre_original' => $fila->nombre_original,
                'hash_sha256' => $fila->hash_sha256,
                'numero_version' => $fila->numero_version,
                'comentario_version' => $fila->comentario_version,
                'subido_por' => $resolverUsuario($fila->subido_por),
            ]);
            $version->forceFill(['created_at' => $fila->created_at, 'updated_at' => $fila->updated_at])->save();

            $nuevaVersionIdPorLegacy[$fila->id] = $version->id;
            $versionesCreadas++;
        }

        // Segunda pasada: version_actual_id se resuelve del puntero real del
        // legacy (no de "la última procesada"), por si alguna vez alguien
        // restauró ahí una versión anterior como vigente.
        foreach ($documentoIdPorLegacy as $documentoIdLegacy => $documentoIdNuevo) {
            $versionActualLegacy = $versionActualLegacyPorDocumentoLegacy[$documentoIdLegacy] ?? null;
            $versionActualNueva = $versionActualLegacy ? ($nuevaVersionIdPorLegacy[$versionActualLegacy] ?? null) : null;

            DocumentoAcervo::where('id', $documentoIdNuevo)->update(['version_actual_id' => $versionActualNueva]);
        }

        $this->command?->info(sprintf(
            '%d tipos, %d documentos y %d versiones migrados desde sios_app_web (%d archivos copiados, %d referenciados pero no encontrados en disco).',
            count($tipoIdPorLegacy),
            $documentosCreados,
            $versionesCreadas,
            $archivosCopiados,
            $archivosFaltantes,
        ));
    }
}
