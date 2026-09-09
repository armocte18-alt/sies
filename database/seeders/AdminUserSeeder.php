<?php

namespace Database\Seeders;

use App\Models\Coordinacion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Crea el usuario administrador inicial del sistema.
     * La contraseña se genera aleatoriamente y se imprime una sola vez en consola;
     * debe cambiarse de inmediato tras el primer inicio de sesión.
     */
    public function run(): void
    {
        $email = env('SIOS_ADMIN_EMAIL', 'admin@sios.local');

        if (User::where('email', $email)->exists()) {
            return;
        }

        $password = env('SIOS_ADMIN_PASSWORD') ?: Str::password(16);

        $admin = User::create([
            'name' => 'Administrador Del Sistema',
            'email' => $email,
            'password' => $password,
            'coordinacion_id' => Coordinacion::where('clave', 'administracion')->value('id'),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('administrador');

        $this->command?->warn("Usuario administrador creado: {$email}");
        $this->command?->warn("Contraseña temporal: {$password}");
        $this->command?->warn('Cambia esta contraseña inmediatamente después del primer inicio de sesión.');
    }
}
