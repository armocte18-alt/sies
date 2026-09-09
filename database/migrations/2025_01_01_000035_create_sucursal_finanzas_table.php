<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sucursal_finanzas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->unique()
                ->constrained('sucursales')->cascadeOnDelete();
            $table->decimal('limite_existencia_caja', 12, 2)->default(0);
            $table->unsignedInteger('volumen_total')->default(0);
            $table->decimal('cantidad_situada', 14, 2)->default(0);
            $table->decimal('ingreso_estimado', 14, 2)->default(0);
            $table->decimal('gasto_total', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursal_finanzas');
    }
};
