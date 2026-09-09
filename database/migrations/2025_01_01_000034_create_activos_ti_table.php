<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activos_ti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            $table->enum('tipo', [
                'computadora', 'impresora', 'servidor', 'camara', 'switch', 'router', 'otro',
            ]);
            $table->string('etiqueta_inventario', 60)->nullable();
            $table->string('marca', 60)->nullable();
            $table->string('modelo', 60)->nullable();
            $table->string('numero_serie', 60)->nullable();
            $table->enum('estado', ['operativo', 'mantenimiento', 'baja'])->default('operativo');
            $table->date('fecha_asignacion')->nullable();
            $table->timestamps();

            $table->index(['sucursal_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activos_ti');
    }
};
