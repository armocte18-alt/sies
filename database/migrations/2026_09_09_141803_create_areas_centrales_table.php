<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas_centrales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('puesto', 150)->nullable();
            $table->string('adscripcion', 150);
            $table->foreignId('gerencia_id')->nullable()->constrained('gerencias')->nullOnDelete();
            $table->string('telefono', 20)->nullable();
            $table->string('extension', 10)->nullable();
            $table->string('correo_finabien');
            $table->string('correo_sigitel')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas_centrales');
    }
};
