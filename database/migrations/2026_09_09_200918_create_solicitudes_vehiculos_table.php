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
        Schema::create('solicitudes_vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitante_id')->constrained('users')->cascadeOnDelete();
            $table->string('numero_empleado', 20)->nullable();
            $table->enum('area', ['gerencia_estatal_cdmx', 'areas_centrales_disfo', 'otro']);
            $table->string('area_otro', 255)->nullable();
            $table->dateTime('fecha_salida_desde');
            $table->dateTime('fecha_salida_hasta');
            $table->json('destinos');
            $table->string('motivo', 500);
            $table->enum('estatus', ['pendiente', 'autorizada', 'rechazada', 'finalizada'])->default('pendiente');
            $table->foreignId('autorizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('autorizado_at')->nullable();
            $table->string('motivo_rechazo', 500)->nullable();
            $table->timestamps();

            $table->index('estatus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_vehiculos');
    }
};
