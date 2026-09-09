<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Algunas circulares reales traen el cuerpo completo del aviso en este
        // campo (varios párrafos), no solo un asunto corto; VARCHAR(500) se
        // quedaba corto. Sin doctrine/dbal, se usa SQL crudo para el ALTER.
        // SQLite no aplica límites de longitud a columnas de texto (los
        // usados en pruebas), así que ahí no hace falta ALTER alguno.
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE circulares MODIFY asunto TEXT NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE circulares MODIFY asunto VARCHAR(500) NOT NULL');
        }
    }
};
