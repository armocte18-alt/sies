<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlaceholderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\Sucursales\EquipamientoController;
use App\Http\Controllers\Sucursales\FinanzasController;
use App\Http\Controllers\Sucursales\HorariosController;
use App\Http\Controllers\Sucursales\InmuebleController;
use App\Http\Controllers\Sucursales\UbicacionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('sucursales')->name('sucursales.')->group(function () {
        Route::get('/', [SucursalController::class, 'index'])->name('index');
        Route::get('/nueva', [SucursalController::class, 'create'])->name('create');
        Route::post('/', [SucursalController::class, 'store'])->name('store');
        Route::get('/{sucursal}', [SucursalController::class, 'show'])->name('show');

        Route::patch('/{sucursal}/identificacion', [SucursalController::class, 'updateIdentificacion'])->name('identificacion.update');
        Route::patch('/{sucursal}/ubicacion', [UbicacionController::class, 'update'])->name('ubicacion.update');
        Route::patch('/{sucursal}/horarios', [HorariosController::class, 'update'])->name('horarios.update');
        Route::patch('/{sucursal}/inmueble', [InmuebleController::class, 'update'])->name('inmueble.update');
        Route::patch('/{sucursal}/equipamiento', [EquipamientoController::class, 'update'])->name('equipamiento.update');
        Route::patch('/{sucursal}/finanzas', [FinanzasController::class, 'update'])->name('finanzas.update');
    });

    // Módulos del menú pendientes de desarrollo: la ruta ya queda protegida
    // por el permiso real que tendrá cada módulo cuando se implemente.
    $modulosPendientes = [
        ['uri' => 'directorios', 'name' => 'directorios.index', 'permission' => 'directorios.ver', 'titulo' => 'Directorios'],
        ['uri' => 'minutarios', 'name' => 'minutarios.index', 'permission' => 'minutarios.ver', 'titulo' => 'Minutarios'],
        ['uri' => 'empleados', 'name' => 'empleados.index', 'permission' => 'empleados.ver', 'titulo' => 'Empleados'],
        ['uri' => 'circulares', 'name' => 'circulares.index', 'permission' => 'circulares.ver', 'titulo' => 'Circulares'],
        ['uri' => 'calendario', 'name' => 'calendario.index', 'permission' => 'calendario.ver', 'titulo' => 'Calendario de Eventos'],
        ['uri' => 'tarjetas', 'name' => 'tarjetas.index', 'permission' => 'tarjetas.ver', 'titulo' => 'Control de Tarjetas'],
        ['uri' => 'vehiculos', 'name' => 'vehiculos.index', 'permission' => 'vehiculos.ver', 'titulo' => 'Vehículos Oficiales'],
        ['uri' => 'mantenimientos', 'name' => 'mantenimientos.index', 'permission' => 'mantenimientos.ver', 'titulo' => 'Mantenimientos'],
        ['uri' => 'metas', 'name' => 'metas.index', 'permission' => 'metas.ver', 'titulo' => 'Metas y Análisis'],
        ['uri' => 'kardex', 'name' => 'kardex.index', 'permission' => 'kardex.gestionar', 'titulo' => 'Ajustes Kárdex'],
        ['uri' => 'accesos', 'name' => 'accesos.index', 'permission' => 'accesos.gestionar', 'titulo' => 'Control de Accesos'],
    ];

    foreach ($modulosPendientes as $modulo) {
        Route::get("/{$modulo['uri']}", PlaceholderController::class)
            ->name($modulo['name'])
            ->middleware("can:{$modulo['permission']}")
            ->defaults('titulo', $modulo['titulo'])
            ->defaults('descripcion', 'Este módulo está planificado para una siguiente fase de desarrollo del sistema SIES.');
    }
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
});

require __DIR__.'/auth.php';
