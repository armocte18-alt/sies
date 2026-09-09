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
        Schema::create('mantenimiento_materiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mantenimiento_id')->constrained('mantenimientos')->cascadeOnDelete();
            $table->string('nombre_material', 150);
            $table->string('unidad', 30)->default('pza');
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('costo_unitario', 12, 2)->default(0);
            $table->decimal('costo_total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_materiales');
    }
};
