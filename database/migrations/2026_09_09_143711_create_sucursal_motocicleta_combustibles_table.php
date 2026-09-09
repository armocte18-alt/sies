<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Historial de cargas de combustible por motocicleta.
        Schema::create('sucursal_motocicleta_combustibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motocicleta_id')->constrained('sucursal_motocicletas')->cascadeOnDelete();
            $table->date('fecha');
            $table->decimal('litros', 6, 2)->nullable();
            $table->decimal('monto', 10, 2)->nullable();
            $table->unsignedInteger('kilometraje')->nullable();
            $table->text('notas')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursal_motocicleta_combustibles');
    }
};
