<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('niveles_salariales', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('consecutivo')->unique();
            $table->string('nivel_salarial', 50)->unique();
            $table->decimal('sueldo_base', 10, 2);
            $table->decimal('compensacion_garantizada', 10, 2)->default(0);
            $table->string('observaciones', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('niveles_salariales');
    }
};
