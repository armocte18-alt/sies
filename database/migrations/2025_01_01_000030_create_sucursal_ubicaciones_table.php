<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sucursal_ubicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->unique()
                ->constrained('sucursales')->cascadeOnDelete();
            $table->string('calle', 150);
            $table->string('num_ext', 20)->nullable();
            $table->string('num_int', 20)->nullable();
            $table->string('colonia', 150);
            $table->foreignId('alcaldia_id')->constrained('alcaldias');
            $table->string('codigo_postal', 5);
            $table->string('entre_calle_1', 150)->nullable();
            $table->string('entre_calle_2', 150)->nullable();
            $table->string('referencia_visual', 255)->nullable();
            $table->decimal('latitud', 10, 6)->nullable();
            $table->decimal('longitud', 10, 6)->nullable();
            $table->string('clave_geografica_inegi', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursal_ubicaciones');
    }
};
