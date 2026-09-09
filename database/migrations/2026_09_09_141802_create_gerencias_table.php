<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gerencias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('coordinacion', 150);
            $table->string('extension', 10)->nullable();
            $table->string('correo_finabien');
            $table->string('correo_sigitel')->nullable();
            $table->string('comite', 150)->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gerencias');
    }
};
