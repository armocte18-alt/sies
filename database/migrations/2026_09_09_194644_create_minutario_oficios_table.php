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
        Schema::create('minutario_oficios', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('consecutivo');
            $table->unsignedSmallInteger('anio');
            $table->string('nomenclatura', 30)->unique();
            $table->text('asunto');
            $table->string('dirigido_a', 255);
            $table->date('fecha_emision');
            $table->string('nombre_solicitante', 255)->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('es_cancelado')->default(false);
            $table->boolean('ya_escaneado')->default(false);
            $table->text('motivo_cancelacion')->nullable();
            $table->string('cancelado_por_nombre', 150)->nullable();
            $table->timestamp('fecha_cancelacion')->nullable();
            $table->timestamps();

            $table->unique(['anio', 'consecutivo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minutario_oficios');
    }
};
