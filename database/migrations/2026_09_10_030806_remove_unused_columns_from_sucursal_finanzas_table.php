<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * volumen_total, cantidad_situada, ingreso_estimado y gasto_total no
     * existen en ningún campo de la sucursal real de sios_app_web — se
     * habían agregado como suposición antes de consultar los datos reales.
     * El usuario confirmó que la coordinación de Finanzas solo necesita
     * "límite de existencia en caja".
     */
    public function up(): void
    {
        Schema::table('sucursal_finanzas', function (Blueprint $table) {
            $table->dropColumn(['volumen_total', 'cantidad_situada', 'ingreso_estimado', 'gasto_total']);
        });
    }

    public function down(): void
    {
        Schema::table('sucursal_finanzas', function (Blueprint $table) {
            $table->unsignedInteger('volumen_total')->default(0);
            $table->decimal('cantidad_situada', 14, 2)->default(0);
            $table->decimal('ingreso_estimado', 14, 2)->default(0);
            $table->decimal('gasto_total', 14, 2)->default(0);
        });
    }
};
