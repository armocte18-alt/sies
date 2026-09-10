<?php

namespace Tests\Feature\Sucursales;

use App\Models\DocumentoAcervo;
use App\Models\DocumentoAcervoVersion;
use App\Models\Sucursal;
use App\Models\TipoDocumentoAcervo;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AcervoDocumentalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        Storage::fake('acervo_documental');
    }

    private function administracion(): User
    {
        $user = User::factory()->create();
        $user->assignRole('administracion');

        return $user;
    }

    public function test_user_without_permission_cannot_upload(): void
    {
        $sucursal = Sucursal::factory()->create();
        $tipo = TipoDocumentoAcervo::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('sucursales.acervo.store', $sucursal), [
                'tipo_documento_id' => $tipo->id,
                'archivo' => UploadedFile::fake()->create('doc.pdf', 100),
            ])
            ->assertForbidden();
    }

    public function test_first_upload_creates_documento_and_version_1(): void
    {
        $sucursal = Sucursal::factory()->create();
        $tipo = TipoDocumentoAcervo::factory()->create();
        $user = $this->administracion();

        $this->actingAs($user)->post(route('sucursales.acervo.store', $sucursal), [
            'tipo_documento_id' => $tipo->id,
            'archivo' => UploadedFile::fake()->create('poliza.pdf', 100),
        ])->assertRedirect();

        $this->assertDatabaseHas('documentos_acervo', [
            'sucursal_id' => $sucursal->id,
            'tipo_documento_id' => $tipo->id,
            'estatus' => 'vigente',
        ]);

        $documento = DocumentoAcervo::first();
        $this->assertNotNull($documento->version_actual_id);
        $this->assertSame(1, $documento->versionActual->numero_version);
        Storage::disk('acervo_documental')->assertExists($documento->versionActual->ruta_archivo);
    }

    public function test_second_upload_for_the_same_type_creates_version_2_and_keeps_version_1(): void
    {
        $sucursal = Sucursal::factory()->create();
        $tipo = TipoDocumentoAcervo::factory()->create();
        $user = $this->administracion();

        $this->actingAs($user)->post(route('sucursales.acervo.store', $sucursal), [
            'tipo_documento_id' => $tipo->id,
            'archivo' => UploadedFile::fake()->create('v1.pdf', 100),
        ]);
        $this->actingAs($user)->post(route('sucursales.acervo.store', $sucursal), [
            'tipo_documento_id' => $tipo->id,
            'archivo' => UploadedFile::fake()->create('v2.pdf', 100),
        ]);

        $documento = DocumentoAcervo::first();
        $this->assertSame(2, $documento->versiones()->count());
        $this->assertSame(2, $documento->fresh()->versionActual->numero_version);
    }

    public function test_can_restore_an_older_version_as_current(): void
    {
        $sucursal = Sucursal::factory()->create();
        $tipo = TipoDocumentoAcervo::factory()->create();
        $user = $this->administracion();

        $this->actingAs($user)->post(route('sucursales.acervo.store', $sucursal), [
            'tipo_documento_id' => $tipo->id,
            'archivo' => UploadedFile::fake()->create('v1.pdf', 100),
        ]);
        $primeraVersion = DocumentoAcervoVersion::first();

        $this->actingAs($user)->post(route('sucursales.acervo.store', $sucursal), [
            'tipo_documento_id' => $tipo->id,
            'archivo' => UploadedFile::fake()->create('v2.pdf', 100),
        ]);

        $this->actingAs($user)
            ->patch(route('sucursales.acervo.versiones.restaurar', $primeraVersion))
            ->assertRedirect();

        $this->assertSame($primeraVersion->id, DocumentoAcervo::first()->fresh()->version_actual_id);
    }

    public function test_only_administrador_can_delete_a_version(): void
    {
        $sucursal = Sucursal::factory()->create();
        $tipo = TipoDocumentoAcervo::factory()->create();
        $gestor = $this->administracion();

        $this->actingAs($gestor)->post(route('sucursales.acervo.store', $sucursal), [
            'tipo_documento_id' => $tipo->id,
            'archivo' => UploadedFile::fake()->create('v1.pdf', 100),
        ]);
        $version = DocumentoAcervoVersion::first();

        $this->actingAs($gestor)
            ->delete(route('sucursales.acervo.versiones.destroy', $version))
            ->assertForbidden();

        $admin = User::factory()->create();
        $admin->assignRole('administrador');

        $this->actingAs($admin)
            ->delete(route('sucursales.acervo.versiones.destroy', $version))
            ->assertRedirect();

        $this->assertDatabaseMissing('documento_acervo_versiones', ['id' => $version->id]);
    }

    public function test_download_requires_being_able_to_view_the_sucursal(): void
    {
        $sucursal = Sucursal::factory()->create();
        $tipo = TipoDocumentoAcervo::factory()->create();
        $gestor = $this->administracion();

        $this->actingAs($gestor)->post(route('sucursales.acervo.store', $sucursal), [
            'tipo_documento_id' => $tipo->id,
            'archivo' => UploadedFile::fake()->create('v1.pdf', 100),
        ]);
        $version = DocumentoAcervoVersion::first();

        $sinPermiso = User::factory()->create();
        $this->actingAs($sinPermiso)
            ->get(route('sucursales.acervo.versiones.descargar', $version))
            ->assertForbidden();

        $this->actingAs($gestor)
            ->get(route('sucursales.acervo.versiones.descargar', $version))
            ->assertOk();
    }

    public function test_can_quick_add_a_new_tipo_documento(): void
    {
        $user = $this->administracion();

        $this->actingAs($user)
            ->post(route('sucursales.acervo.tipos.store'), ['nombre' => 'Nuevo Tipo De Prueba'])
            ->assertRedirect();

        $this->assertDatabaseHas('tipos_documento_acervo', ['nombre' => 'Nuevo Tipo De Prueba']);
    }
}
