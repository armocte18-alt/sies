<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sucursal_inmuebles', function (Blueprint $table) {
            $table->enum('tipo_caja_fuerte', ['disco', 'llave'])->nullable()->after('vigencia_proteccion_civil');
            $table->string('modelo_caja_fuerte', 100)->nullable()->after('tipo_caja_fuerte');
            $table->string('numero_inventario_caja_fuerte', 60)->nullable()->after('modelo_caja_fuerte');
            $table->boolean('caja_fuerte_tiene_llave')->default(false)->after('numero_inventario_caja_fuerte');
        });
    }

    public function down(): void
    {
        Schema::table('sucursal_inmuebles', function (Blueprint $table) {
            $table->dropColumn(['tipo_caja_fuerte', 'modelo_caja_fuerte', 'numero_inventario_caja_fuerte', 'caja_fuerte_tiene_llave']);
        });
    }
};
