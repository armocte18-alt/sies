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
        Schema::create('eventos_calendario', function (Blueprint $table) {
            $table->id();
            $table->uuid('grupo_recurrencia_id')->nullable();
            $table->string('nombre', 255);
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->string('ubicacion', 255)->nullable();
            $table->enum('tipo_asociado', ['area_central', 'gerencia', 'sucursal', 'externo'])->nullable();
            $table->string('asociado_nombre', 255)->nullable();
            $table->string('color', 20)->default('#135c46');
            $table->json('invitados')->nullable();
            $table->string('creado_por_nombre', 150)->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index('fecha_inicio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos_calendario');
    }
};
