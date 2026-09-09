<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Inventario general (no por mensajero) del equipo de protección
        // personal de reparto: casco, guantes, botas, chamarra, etc.
        Schema::create('sucursal_equipamiento_repartos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            $table->string('tipo', 60);
            $table->string('talla', 20)->nullable();
            $table->unsignedInteger('cantidad_total')->default(0);
            $table->unsignedInteger('cantidad_buen_estado')->default(0);
            $table->unsignedInteger('cantidad_danado')->default(0);
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->unique(['sucursal_id', 'tipo', 'talla']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursal_equipamiento_repartos');
    }
};
