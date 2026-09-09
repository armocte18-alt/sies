<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sucursal_operaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->unique()
                ->constrained('sucursales')->cascadeOnDelete();
            $table->string('dias_laborables', 60)->nullable();
            $table->time('hora_apertura_publico')->nullable();
            $table->time('hora_cierre_publico')->nullable();
            $table->time('hora_inicio_labores_interno')->nullable();
            $table->time('hora_fin_labores_interno')->nullable();
            $table->enum('tipo_poblacion', [
                'urbana_alta_densidad', 'urbana_media_densidad', 'urbana_baja_densidad', 'rural',
            ])->nullable();
            $table->enum('tipo_inmueble', ['propio', 'arrendado', 'comodato', 'otro'])->nullable();
            $table->string('comunicacion', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->boolean('reparto_activo')->default(false);
            $table->string('enrutamiento', 60)->nullable();
            $table->string('dias_guardia', 60)->nullable();
            $table->time('apertura_guardia')->nullable();
            $table->time('cierre_guardia')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursal_operaciones');
    }
};
