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
        Schema::create('metas_mensuales', function (Blueprint $table) {
            $table->id();
            $table->string('linea_negocio', 255);
            $table->unsignedSmallInteger('anio');
            $table->enum('tipo', ['core', 'especial']);
            foreach ([
                'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre',
            ] as $mes) {
                $table->decimal($mes, 14, 2)->default(0);
            }
            $table->decimal('meta_anual', 16, 2)->default(0);
            $table->string('producto_claves_vinculadas', 255)->nullable();
            $table->timestamps();

            $table->unique(['linea_negocio', 'anio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metas_mensuales');
    }
};
