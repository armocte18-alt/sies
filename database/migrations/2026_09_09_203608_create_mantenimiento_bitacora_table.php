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
        Schema::create('mantenimiento_bitacora', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mantenimiento_id')->constrained('mantenimientos')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('accion', 60);
            $table->text('comentario')->nullable();
            $table->string('valor_anterior', 255)->nullable();
            $table->string('valor_nuevo', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_bitacora');
    }
};
