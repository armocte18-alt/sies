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
        Schema::create('tarjetas_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarjeta_id')->constrained('tarjetas_inventario')->cascadeOnDelete();
            $table->string('accion', 60);
            $table->string('estatus_anterior', 30);
            $table->string('estatus_nuevo', 30);
            $table->string('motivo', 500)->nullable();
            $table->string('usuario', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarjetas_historial');
    }
};
