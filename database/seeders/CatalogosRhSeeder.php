<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use App\Models\Labor;
use App\Models\NivelEducativo;
use App\Models\Puesto;
use App\Models\TipoControlAsistencia;
use App\Models\TipoNombramiento;
use Illuminate\Database\Seeder;

class CatalogosRhSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCatalog(TipoNombramiento::class, ['base', 'confianza']);

        $this->seedCatalog(TipoControlAsistencia::class, ['lista', 'biométrico']);

        $this->seedCatalog(NivelEducativo::class, [
            'ninguna',
            'primaria',
            'secundaria',
            'bachillerato',
            'carrera técnica',
            'técnico superior universitario',
            'licenciatura',
            'maestría',
            'doctorado',
        ]);

        $this->seedCatalog(Especialidad::class, [
            'administración',
            'derecho',
            'contaduría y finanzas',
            'tecnologías de la información',
            'ingeniería',
            'recursos humanos',
            'comunicación',
            'psicología',
            'economía',
            'mercadotecnia',
            'arquitectura',
            'trabajo social',
            'ciencias políticas',
            'pedagogía',
            'otra',
        ]);

        $this->seedCatalog(Puesto::class, [
            'administrador de proyectos de telecomunicaciones',
            'jefe de oficina tipo a',
        ]);

        $this->seedCatalog(Labor::class, [
            'no especificada',
            'gerente',
            'coordinación',
            'administrativas',
            'jefe de sucursal',
            'enc. de sucursal',
            'operativas',
            'reparto',
            'técnico',
            'mantenimiento',
            'sindicato',
        ]);
    }

    /**
     * @param  class-string  $model
     * @param  string[]  $nombres
     */
    private function seedCatalog(string $model, array $nombres): void
    {
        foreach ($nombres as $orden => $nombre) {
            $model::updateOrCreate(
                ['nombre' => mb_strtolower($nombre)],
                ['orden' => $orden + 1, 'activo' => true],
            );
        }
    }
}
