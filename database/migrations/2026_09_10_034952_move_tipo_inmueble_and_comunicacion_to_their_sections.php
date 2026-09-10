<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Tipo de inmueble" y "Comunicación" vivían en Operación y horarios,
     * pero conceptualmente pertenecen a Inmueble (Administración) y
     * Equipamiento técnico (Técnica) respectivamente. Mueve las columnas
     * conservando los datos reales ya capturados (60 sucursales en ambos
     * casos) antes de borrar el origen.
     */
    public function up(): void
    {
        Schema::table('sucursal_inmuebles', function (Blueprint $table) {
            $table->enum('tipo_inmueble', ['propio', 'arrendado', 'comodato', 'otro'])->nullable()->after('sucursal_id');
        });

        Schema::table('sucursal_equipamientos', function (Blueprint $table) {
            $table->string('comunicacion', 100)->nullable()->after('sucursal_id');
        });

        // El copiado de datos reales solo aplica a la base de datos viva
        // (MySQL); las pruebas usan SQLite en memoria y siempre parten de
        // una base vacía, así que no hay nada que trasladar ahí.
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('
                UPDATE sucursal_inmuebles si
                INNER JOIN sucursal_operaciones so ON so.sucursal_id = si.sucursal_id
                SET si.tipo_inmueble = so.tipo_inmueble
                WHERE so.tipo_inmueble IS NOT NULL
            ');

            // sucursal_equipamientos es 1:1 opcional y, a diferencia de
            // sucursal_inmuebles, ninguna sucursal tenía fila todavía — no hay
            // nada que UPDATE-JOIN, hay que insertar la fila con el resto de
            // columnas en su valor por defecto.
            DB::statement("
                INSERT INTO sucursal_equipamientos (sucursal_id, comunicacion, created_at, updated_at)
                SELECT so.sucursal_id, so.comunicacion, NOW(), NOW()
                FROM sucursal_operaciones so
                WHERE so.comunicacion IS NOT NULL
                  AND NOT EXISTS (SELECT 1 FROM sucursal_equipamientos se WHERE se.sucursal_id = so.sucursal_id)
            ");
        }

        Schema::table('sucursal_operaciones', function (Blueprint $table) {
            $table->dropColumn(['tipo_inmueble', 'comunicacion']);
        });
    }

    public function down(): void
    {
        Schema::table('sucursal_operaciones', function (Blueprint $table) {
            $table->enum('tipo_inmueble', ['propio', 'arrendado', 'comodato', 'otro'])->nullable();
            $table->string('comunicacion', 100)->nullable();
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('
                UPDATE sucursal_operaciones so
                INNER JOIN sucursal_inmuebles si ON si.sucursal_id = so.sucursal_id
                SET so.tipo_inmueble = si.tipo_inmueble
            ');

            DB::statement('
                UPDATE sucursal_operaciones so
                INNER JOIN sucursal_equipamientos se ON se.sucursal_id = so.sucursal_id
                SET so.comunicacion = se.comunicacion
            ');
        }

        Schema::table('sucursal_inmuebles', function (Blueprint $table) {
            $table->dropColumn('tipo_inmueble');
        });

        Schema::table('sucursal_equipamientos', function (Blueprint $table) {
            $table->dropColumn('comunicacion');
        });
    }
};
