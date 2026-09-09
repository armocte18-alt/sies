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
        Schema::create('mantenimiento_personal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mantenimiento_id')->constrained('mantenimientos')->cascadeOnDelete();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->string('rol_en_mantenimiento', 100)->nullable();
            $table->timestamps();

            $table->unique(['mantenimiento_id', 'empleado_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_personal');
    }
};
