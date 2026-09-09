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
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('marca', 255);
            $table->string('modelo', 255);
            $table->unsignedSmallInteger('anio');
            $table->enum('tipo', ['propio', 'arrendado', 'otro']);
            $table->string('tipo_otro', 255)->nullable();
            $table->string('placa', 15)->unique();
            $table->string('folio_tarjeta_circulacion', 255)->nullable();
            $table->date('vigencia_tarjeta_circulacion')->nullable();
            $table->string('folio_poliza_seguro', 255)->nullable();
            $table->date('vigencia_poliza_seguro')->nullable();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->unsignedInteger('kilometraje_actual')->default(0);
            $table->unsignedTinyInteger('combustible_inicial')->default(0);
            $table->json('equipo_seguridad')->nullable();
            $table->text('condicion_notas')->nullable();
            $table->date('fecha_ultima_verificacion')->nullable();
            $table->enum('tipo_holograma', ['00', '0', '1', '2', 'exento'])->nullable();
            $table->date('fecha_ultimo_servicio')->nullable();
            $table->enum('estatus', ['disponible', 'asignado', 'mantenimiento', 'fuera_de_servicio'])->default('disponible');
            $table->timestamps();

            $table->index('estatus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
