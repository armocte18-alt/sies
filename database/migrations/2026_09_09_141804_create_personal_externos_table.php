<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_externos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('puesto', 150)->nullable();
            $table->string('dependencia', 150);
            $table->string('adscripcion', 150)->nullable();
            $table->string('domicilio', 255)->nullable();
            $table->json('telefonos')->nullable();
            $table->json('correos')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_externos');
    }
};
