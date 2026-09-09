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
        Schema::create('solicitud_vehiculo_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->unique()->constrained('solicitudes_vehiculos')->cascadeOnDelete();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->cascadeOnDelete();
            $table->foreignId('conductor_id')->constrained('conductores')->cascadeOnDelete();
            $table->unsignedInteger('km_inicial')->default(0);
            $table->unsignedInteger('km_final')->nullable();
            $table->unsignedInteger('km_recorridos')->nullable();
            $table->unsignedTinyInteger('combustible_entrega')->nullable();
            $table->unsignedTinyInteger('combustible_recepcion')->nullable();
            $table->text('carroceria_notas_previas')->nullable();
            $table->text('carroceria_notas_nuevas')->nullable();
            $table->boolean('checklist_gato')->default(false);
            $table->boolean('checklist_llave_cruz')->default(false);
            $table->boolean('checklist_reflejantes')->default(false);
            $table->boolean('checklist_extintor')->default(false);
            $table->dateTime('fecha_hora_devolucion')->nullable();
            $table->text('observaciones_fallas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_vehiculo_detalles');
    }
};
