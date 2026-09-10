<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * "panel_alarma" y "sensor_movimiento" se agregan como tipos de activo
     * para que el equipo de seguridad (además de cámaras) también pueda
     * llevar modelo/serie/número de inventario en el inventario detallado
     * — el mismo mecanismo que ya existía para equipos de cómputo.
     * Se modifica el ENUM con SQL directo porque Schema::table()->enum()
     * no soporta ALTER de un enum existente sin doctrine/dbal. Solo aplica
     * a MySQL (la base viva); SQLite (pruebas) recrea la tabla desde cero
     * en cada corrida y ya incluye estos valores en su CHECK constraint
     * porque el enum final se declaró directamente en la migración que
     * crea la tabla.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE activos_ti
                MODIFY tipo ENUM('computadora', 'impresora', 'servidor', 'camara', 'panel_alarma', 'sensor_movimiento', 'switch', 'router', 'otro')
            ");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE activos_ti
                MODIFY tipo ENUM('computadora', 'impresora', 'servidor', 'camara', 'switch', 'router', 'otro')
            ");
        }
    }
};
