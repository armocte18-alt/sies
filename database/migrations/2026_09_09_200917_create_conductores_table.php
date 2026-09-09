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
        Schema::create('conductores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->nullable()->constrained('empleados')->nullOnDelete();
            $table->string('nombre_completo', 255);
            $table->string('numero_empleado', 20);
            $table->string('area_adscripcion', 255)->nullable();
            $table->string('numero_licencia', 20)->unique();
            $table->enum('tipo_licencia', ['A1', 'A2', 'B', 'C', 'D', 'E', 'E1', 'SICT_A', 'SICT_B', 'SICT_C', 'SICT_D', 'SICT_E', 'SICT_F']);
            $table->boolean('es_permanente')->default(false);
            $table->date('vigencia_licencia')->nullable();
            $table->enum('estatus', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conductores');
    }
};
