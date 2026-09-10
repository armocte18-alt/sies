<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documento_acervo_versiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos_acervo')->cascadeOnDelete();
            $table->string('ruta_archivo', 255);
            $table->string('nombre_original', 255);
            $table->string('hash_sha256', 64);
            $table->unsignedInteger('numero_version');
            $table->text('comentario_version')->nullable();
            $table->foreignId('subido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_acervo_versiones');
    }
};
