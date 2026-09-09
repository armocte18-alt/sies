<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sucursal_inmuebles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->unique()
                ->constrained('sucursales')->cascadeOnDelete();
            $table->enum('tipo_contrato_posesion', ['propio', 'arrendado', 'comodato', 'otro'])->nullable();
            $table->decimal('superficie_m2', 10, 2)->nullable();
            $table->string('medidas', 100)->nullable();
            $table->date('fecha_inicio_contrato')->nullable();
            $table->date('fecha_fin_contrato')->nullable();
            $table->decimal('monto_renta_mensual', 12, 2)->nullable();
            $table->string('propietario_arrendador', 150)->nullable();
            $table->string('numero_escritura_contrato', 60)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursal_inmuebles');
    }
};
