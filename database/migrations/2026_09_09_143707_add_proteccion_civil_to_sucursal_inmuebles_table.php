<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sucursal_inmuebles', function (Blueprint $table) {
            $table->boolean('cuenta_proteccion_civil')->default(false)->after('numero_escritura_contrato');
            $table->string('numero_dictamen_proteccion_civil', 60)->nullable()->after('cuenta_proteccion_civil');
            $table->date('vigencia_proteccion_civil')->nullable()->after('numero_dictamen_proteccion_civil');
        });
    }

    public function down(): void
    {
        Schema::table('sucursal_inmuebles', function (Blueprint $table) {
            $table->dropColumn(['cuenta_proteccion_civil', 'numero_dictamen_proteccion_civil', 'vigencia_proteccion_civil']);
        });
    }
};
