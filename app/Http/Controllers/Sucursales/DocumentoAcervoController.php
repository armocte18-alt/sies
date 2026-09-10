<?php

namespace App\Http\Controllers\Sucursales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sucursales\StoreDocumentoAcervoRequest;
use App\Http\Requests\Sucursales\StoreTipoDocumentoAcervoRequest;
use App\Models\DocumentoAcervo;
use App\Models\DocumentoAcervoVersion;
use App\Models\Sucursal;
use App\Models\TipoDocumentoAcervo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentoAcervoController extends Controller
{
    private const DISCO = 'acervo_documental';

    /**
     * Sube un archivo: crea el DocumentoAcervo si es la primera vez que se
     * carga algo para ese tipo en esta sucursal, o agrega una nueva versión
     * conservando las anteriores intactas — igual que en sios-app-web.
     */
    public function store(StoreDocumentoAcervoRequest $request, Sucursal $sucursal): RedirectResponse
    {
        $datos = $request->validated();

        $documento = DocumentoAcervo::firstOrCreate(
            ['sucursal_id' => $sucursal->id, 'tipo_documento_id' => $datos['tipo_documento_id']],
            [
                'folio' => $datos['folio'] ?? null,
                'fecha_documento' => $datos['fecha_documento'] ?? null,
                'estatus' => 'vigente',
                'creado_por' => Auth::id(),
            ],
        );

        if (! $documento->wasRecentlyCreated) {
            $documento->update([
                'folio' => $datos['folio'] ?? $documento->folio,
                'fecha_documento' => $datos['fecha_documento'] ?? $documento->fecha_documento,
            ]);
        }

        $archivo = $request->file('archivo');
        $siguienteVersion = ($documento->versiones()->max('numero_version') ?? 0) + 1;

        $rutaCarpeta = "sucursal_{$sucursal->id}/tipo_{$datos['tipo_documento_id']}";
        $nombreArchivo = now()->format('YmdHis').'_v'.$siguienteVersion.'.'.$archivo->getClientOriginalExtension();
        $rutaGuardada = $archivo->storeAs($rutaCarpeta, $nombreArchivo, self::DISCO);

        $version = DocumentoAcervoVersion::create([
            'documento_id' => $documento->id,
            'ruta_archivo' => $rutaGuardada,
            'nombre_original' => $archivo->getClientOriginalName(),
            'hash_sha256' => hash_file('sha256', $archivo->getRealPath()),
            'numero_version' => $siguienteVersion,
            'comentario_version' => $datos['comentario_version'] ?? null,
            'subido_por' => Auth::id(),
        ]);

        $documento->update(['version_actual_id' => $version->id]);

        return back()->with('status', 'Documento cargado (versión '.$siguienteVersion.').');
    }

    public function storeTipo(StoreTipoDocumentoAcervoRequest $request): RedirectResponse
    {
        TipoDocumentoAcervo::create([
            'nombre' => $request->validated('nombre'),
            'slug' => Str::slug($request->validated('nombre'), '_'),
            'activo' => true,
            'orden' => (TipoDocumentoAcervo::max('orden') ?? 0) + 1,
        ]);

        return back()->with('status', 'Tipo de documento agregado.');
    }

    /**
     * Descarga con verificación de permisos — nunca una URL pública directa.
     */
    public function descargar(DocumentoAcervoVersion $version): StreamedResponse
    {
        $version->load('documento.sucursal');
        $this->authorize('view', $version->documento->sucursal);

        abort_unless(Storage::disk(self::DISCO)->exists($version->ruta_archivo), 404, 'El archivo ya no existe en el servidor.');

        return Storage::disk(self::DISCO)->download($version->ruta_archivo, $version->nombre_original);
    }

    /**
     * Vuelve a marcar una versión anterior como la vigente — no borra nada,
     * todo el historial se conserva intacto.
     */
    public function restaurarVersion(DocumentoAcervoVersion $version): RedirectResponse
    {
        $version->load('documento.sucursal');
        $this->authorize('updateAcervo', $version->documento->sucursal);

        $version->documento->update(['version_actual_id' => $version->id]);

        return back()->with('status', "Versión {$version->numero_version} restaurada como vigente.");
    }

    /**
     * Elimina una versión por completo (archivo físico + registro). Solo
     * administrador, igual que en sios-app-web (ahí, solo super_administrador).
     */
    public function eliminarVersion(DocumentoAcervoVersion $version): RedirectResponse
    {
        $version->load('documento.sucursal');
        $this->authorize('updateAcervo', $version->documento->sucursal);
        abort_unless(Auth::user()->hasRole('administrador'), 403, 'Solo un administrador puede eliminar una versión del acervo documental.');

        $documento = $version->documento;

        // version_actual_id apunta a esta versión: hay que soltar la
        // referencia antes de borrar la fila, o la FK lo impediría.
        if ($documento->version_actual_id === $version->id) {
            $nuevaActual = $documento->versiones()->where('id', '!=', $version->id)->orderByDesc('numero_version')->first();
            $documento->update(['version_actual_id' => $nuevaActual?->id]);
        }

        if (Storage::disk(self::DISCO)->exists($version->ruta_archivo)) {
            Storage::disk(self::DISCO)->delete($version->ruta_archivo);
        }

        $version->delete();

        return back()->with('status', 'Versión eliminada.');
    }
}
