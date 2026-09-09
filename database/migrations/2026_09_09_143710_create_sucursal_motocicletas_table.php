<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sucursal_motocicletas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            $table->string('placa', 20)->nullable();
            $table->string('marca', 60)->nullable();
            $table->string('modelo', 60)->nullable();
            $table->unsignedSmallInteger('anio')->nullable();
            $table->unsignedInteger('kilometraje_actual')->default(0);
            $table->enum('estado', ['operativa', 'mantenimiento', 'baja'])->default('operativa');
            $table->date('fecha_alta')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursal_motocicletas');
    }
};
