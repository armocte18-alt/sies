<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sucursal_equipamientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->unique()
                ->constrained('sucursales')->cascadeOnDelete();
            $table->unsignedInteger('num_computadoras')->default(0);
            $table->unsignedInteger('num_impresoras')->default(0);
            $table->unsignedInteger('num_servidores')->default(0);
            $table->boolean('tiene_camaras')->default(false);
            $table->unsignedInteger('num_camaras')->default(0);
            $table->boolean('tiene_alarma')->default(false);
            $table->boolean('tiene_extintores')->default(false);
            $table->unsignedInteger('num_extintores')->default(0);
            $table->string('proveedor_internet', 100)->nullable();
            $table->string('tipo_enlace', 100)->nullable();
            $table->string('velocidad_contratada', 50)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursal_equipamientos');
    }
};
