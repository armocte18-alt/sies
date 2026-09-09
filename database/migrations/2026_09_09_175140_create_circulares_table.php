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
        Schema::create('circulares', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 30)->unique();
            $table->string('asunto', 500);
            $table->date('fecha_aplicacion')->nullable();
            $table->string('ambito', 60)->nullable();
            $table->string('enlace_externo', 500)->nullable();
            $table->string('archivo_path', 255)->nullable();
            $table->text('tips')->nullable();
            $table->string('creado_por', 150)->nullable();
            $table->boolean('activo')->default(true);
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('circulares');
    }
};
