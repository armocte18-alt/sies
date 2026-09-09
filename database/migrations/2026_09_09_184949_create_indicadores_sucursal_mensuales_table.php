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
        Schema::create('indicadores_sucursal_mensuales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->string('clave_sucursal_legacy', 20);
            $table->string('nombre_sucursal_legacy', 255)->nullable();
            $table->unsignedTinyInteger('mes');
            $table->unsignedSmallInteger('anio');
            $table->string('edo', 10)->nullable();
            $table->string('region', 50)->nullable();
            $table->unsignedInteger('volumen_total')->default(0);
            $table->decimal('cantidad_situada', 16, 2)->default(0);
            $table->decimal('ingresos_total', 16, 4)->default(0);
            $table->decimal('gasto_total', 16, 4)->default(0);
            $table->decimal('balance', 16, 4)->default(0);
            $table->decimal('rentabilidad', 16, 4)->default(0);
            $table->decimal('productividad', 12, 4)->default(0);
            $table->unsignedSmallInteger('total_empleados')->default(0);
            $table->unsignedTinyInteger('dias_laborados')->default(0);
            $table->decimal('gasto_traslado_valores', 14, 2)->default(0);
            $table->decimal('gasto_servicios_basicos', 14, 2)->default(0);
            $table->string('numero_oficio_carga', 255)->nullable();
            $table->timestamps();

            $table->unique(['clave_sucursal_legacy', 'mes', 'anio'], 'indicadores_sucursal_periodo_unique');
            $table->index(['anio', 'mes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicadores_sucursal_mensuales');
    }
};
