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
        Schema::create('minutario_boletines', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('consecutivo');
            $table->unsignedSmallInteger('anio');
            $table->string('nomenclatura', 30)->unique();
            $table->string('asunto', 255);
            $table->text('contenido');
            $table->date('fecha_elaboracion');
            $table->date('fecha_vigor');
            $table->enum('dirigido_tipo', ['todos', 'gerencia', 'sucursales'])->default('todos');
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fecha_ultima_modificacion')->nullable();
            $table->timestamps();

            $table->unique(['anio', 'consecutivo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minutario_boletines');
    }
};
