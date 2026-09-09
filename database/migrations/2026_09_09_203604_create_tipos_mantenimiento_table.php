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
        Schema::create('tipos_mantenimiento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->string('color', 20)->default('#285c4d');
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_mantenimiento');
    }
};
