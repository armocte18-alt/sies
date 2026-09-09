<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->foreignId('tipo_mantenimiento_id')->constrained('tipos_mantenimiento');
            $table->string('titulo', 200);
            $table->text('descripcion')->nullable();
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->enum('estatus', ['pendiente', 'programado', 'en_proceso', 'completado', 'cancelado'])->default('pendiente');
            $table->date('fecha_programada_inicio');
            $table->date('fecha_programada_fin');
            $table->dateTime('fecha_inicio_real')->nullable();
            $table->dateTime('fecha_fin_real')->nullable();
            $table->decimal('costo_mano_obra', 12, 2)->default(0);
            $table->decimal('costo_materiales', 12, 2)->default(0);
            $table->text('comentario_cierre')->nullable();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cerrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('estatus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
