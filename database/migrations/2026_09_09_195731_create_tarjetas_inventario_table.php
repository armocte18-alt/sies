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
        Schema::create('tarjetas_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('tarjetas_productos')->cascadeOnDelete();
            $table->string('numero_tarjeta', 4);
            $table->string('cuenta', 255)->unique();
            $table->date('fecha_recepcion');
            $table->string('usuario_recibe', 255)->nullable();
            $table->enum('estatus', ['en_stock', 'activa', 'renominada'])->default('en_stock');
            $table->date('fecha_asignacion')->nullable();
            $table->date('fecha_renominacion')->nullable();
            $table->foreignId('destino_sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->string('otro_destino_descripcion', 255)->nullable();
            $table->string('usuario_asigna', 255)->nullable();
            $table->string('numero_oficio', 255)->nullable();
            $table->timestamps();

            $table->index('estatus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarjetas_inventario');
    }
};
