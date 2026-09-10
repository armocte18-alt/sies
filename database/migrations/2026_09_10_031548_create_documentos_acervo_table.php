<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_acervo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            $table->foreignId('tipo_documento_id')->constrained('tipos_documento_acervo');
            $table->string('folio', 255)->nullable();
            $table->date('fecha_documento')->nullable();
            $table->enum('estatus', ['vigente', 'sustituido', 'baja'])->default('vigente');
            // Sin ->constrained(): documento_acervo_versiones todavía no existe en
            // este punto de la migración, y es solo un puntero de conveniencia
            // (la relación real, con historial completo, vive en versiones()).
            $table->unsignedBigInteger('version_actual_id')->nullable();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['sucursal_id', 'tipo_documento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_acervo');
    }
};
