<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_oficial', 150);
            $table->string('clave_financiera', 20)->unique();
            $table->string('centro_distribucion', 20)->nullable();
            $table->enum('estatus_operativo', ['activa', 'inactiva', 'suspendida', 'en_apertura'])
                ->default('en_apertura');
            $table->foreignId('titular_empleado_id')->nullable()
                ->constrained('empleados')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};
