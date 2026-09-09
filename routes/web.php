<?php

use App\Http\Controllers\AccesosController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\CatalogosRhController;
use App\Http\Controllers\CircularController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\MetasAnalisisController;
use App\Http\Controllers\MinutarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DirectorioController;
use App\Http\Controllers\PlaceholderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\Sucursales\EquipamientoController;
use App\Http\Controllers\Sucursales\FinanzasController;
use App\Http\Controllers\Sucursales\HorariosController;
use App\Http\Controllers\Sucursales\InmuebleController;
use App\Http\Controllers\Sucursales\RepartoController;
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
        Route::post('/{sucursal}/activos-ti', [EquipamientoController::class, 'storeActivo'])->name('activos-ti.store');
        Route::put('/{sucursal}/activos-ti/{activo}', [EquipamientoController::class, 'updateActivo'])->name('activos-ti.update');
        Route::delete('/{sucursal}/activos-ti/{activo}', [EquipamientoController::class, 'destroyActivo'])->name('activos-ti.destroy');
        Route::patch('/{sucursal}/finanzas', [FinanzasController::class, 'update'])->name('finanzas.update');

        Route::post('/{sucursal}/motocicletas', [RepartoController::class, 'storeMotocicleta'])->name('motocicletas.store');
        Route::put('/{sucursal}/motocicletas/{motocicleta}', [RepartoController::class, 'updateMotocicleta'])->name('motocicletas.update');
        Route::delete('/{sucursal}/motocicletas/{motocicleta}', [RepartoController::class, 'destroyMotocicleta'])->name('motocicletas.destroy');
        Route::post('/{sucursal}/motocicletas/{motocicleta}/combustible', [RepartoController::class, 'storeCombustible'])->name('motocicletas.combustible.store');

        Route::post('/{sucursal}/equipamiento-reparto', [RepartoController::class, 'storeEquipamiento'])->name('equipamiento-reparto.store');
        Route::put('/{sucursal}/equipamiento-reparto/{equipamientoReparto}', [RepartoController::class, 'updateEquipamiento'])->name('equipamiento-reparto.update');
        Route::delete('/{sucursal}/equipamiento-reparto/{equipamientoReparto}', [RepartoController::class, 'destroyEquipamiento'])->name('equipamiento-reparto.destroy');
    });

    Route::prefix('rh/catalogos')->name('rh.catalogos.')->middleware('can:catalogos-rh.gestionar')->group(function () {
        Route::get('/', [CatalogosRhController::class, 'index'])->name('index');

        Route::post('/niveles-salariales', [CatalogosRhController::class, 'storeNivelSalarial'])->name('niveles-salariales.store');
        Route::put('/niveles-salariales/{nivelSalarial}', [CatalogosRhController::class, 'updateNivelSalarial'])->name('niveles-salariales.update');
        Route::delete('/niveles-salariales/{nivelSalarial}', [CatalogosRhController::class, 'destroyNivelSalarial'])->name('niveles-salariales.destroy');

        Route::post('/{catalogo}', [CatalogosRhController::class, 'storeItem'])
            ->name('items.store')
            ->where('catalogo', \App\Support\CatalogoRhRegistro::patronRuta());
        Route::put('/{catalogo}/{id}', [CatalogosRhController::class, 'updateItem'])
            ->name('items.update')
            ->where('catalogo', \App\Support\CatalogoRhRegistro::patronRuta());
        Route::delete('/{catalogo}/{id}', [CatalogosRhController::class, 'destroyItem'])
            ->name('items.destroy')
            ->where('catalogo', \App\Support\CatalogoRhRegistro::patronRuta());
    });

    Route::prefix('directorios')->name('directorios.')->middleware('can:directorios.ver')->group(function () {
        Route::get('/', [DirectorioController::class, 'index'])->name('index');

        Route::middleware('can:directorios.gestionar')->group(function () {
            Route::post('/gerencias', [DirectorioController::class, 'storeGerencia'])->name('gerencias.store');
            Route::put('/gerencias/{gerencia}', [DirectorioController::class, 'updateGerencia'])->name('gerencias.update');
            Route::patch('/gerencias/{gerencia}/estado', [DirectorioController::class, 'toggleGerencia'])->name('gerencias.estado');

            Route::post('/areas-centrales', [DirectorioController::class, 'storeAreaCentral'])->name('areas.store');
            Route::put('/areas-centrales/{areaCentral}', [DirectorioController::class, 'updateAreaCentral'])->name('areas.update');
            Route::patch('/areas-centrales/{areaCentral}/estado', [DirectorioController::class, 'toggleAreaCentral'])->name('areas.estado');

            Route::post('/externos', [DirectorioController::class, 'storePersonalExterno'])->name('externos.store');
            Route::put('/externos/{personalExterno}', [DirectorioController::class, 'updatePersonalExterno'])->name('externos.update');
            Route::patch('/externos/{personalExterno}/estado', [DirectorioController::class, 'togglePersonalExterno'])->name('externos.estado');
        });
    });

    Route::prefix('circulares')->name('circulares.')->middleware('can:circulares.ver')->group(function () {
        Route::get('/', [CircularController::class, 'index'])->name('index');

        Route::middleware('can:circulares.gestionar')->group(function () {
            Route::post('/', [CircularController::class, 'store'])->name('store');
            Route::put('/{circular}', [CircularController::class, 'update'])->name('update');
            Route::patch('/{circular}/estado', [CircularController::class, 'toggleActive'])->name('estado');
        });
    });

    Route::get('/metas', [MetasAnalisisController::class, 'index'])->name('metas.index')->middleware('can:metas.ver');

    Route::prefix('minutarios')->name('minutarios.')->middleware('can:minutarios.ver')->group(function () {
        Route::get('/', [MinutarioController::class, 'index'])->name('index');

        Route::middleware('can:minutarios.gestionar')->group(function () {
            Route::post('/boletines', [MinutarioController::class, 'storeBoletin'])->name('boletines.store');
            Route::put('/boletines/{boletin}', [MinutarioController::class, 'updateBoletin'])->name('boletines.update');

            Route::post('/oficios', [MinutarioController::class, 'storeOficio'])->name('oficios.store');
            Route::put('/oficios/{oficio}', [MinutarioController::class, 'updateOficio'])->name('oficios.update');
            Route::patch('/oficios/{oficio}/cancelar', [MinutarioController::class, 'cancelOficio'])->name('oficios.cancelar');
            Route::patch('/oficios/{oficio}/escaneo', [MinutarioController::class, 'toggleEscaneoOficio'])->name('oficios.escaneo');
        });
    });

    Route::prefix('empleados')->name('empleados.')->middleware('can:empleados.ver')->group(function () {
        Route::get('/', [EmpleadoController::class, 'index'])->name('index');

        Route::middleware('can:empleados.gestionar')->group(function () {
            Route::post('/', [EmpleadoController::class, 'store'])->name('store');
            Route::put('/{empleado}', [EmpleadoController::class, 'update'])->name('update');
            Route::patch('/{empleado}/estado', [EmpleadoController::class, 'toggleActive'])->name('estado');
        });
    });

    Route::prefix('calendario')->name('calendario.')->middleware('can:calendario.ver')->group(function () {
        Route::get('/', [CalendarioController::class, 'index'])->name('index');
        Route::get('/eventos', [CalendarioController::class, 'eventosJson'])->name('eventos');

        Route::middleware('can:calendario.gestionar')->group(function () {
            Route::post('/eventos', [CalendarioController::class, 'store'])->name('eventos.store');
            Route::put('/eventos/{evento}', [CalendarioController::class, 'update'])->name('eventos.update');
            Route::delete('/eventos/{evento}/serie', [CalendarioController::class, 'destroySerie'])->name('eventos.destroy-serie');
            Route::delete('/eventos/{evento}', [CalendarioController::class, 'destroy'])->name('eventos.destroy');

            Route::get('/eventos-rapidos', [CalendarioController::class, 'rapidosIndex'])->name('rapidos.index');
            Route::post('/eventos-rapidos', [CalendarioController::class, 'rapidosStore'])->name('rapidos.store');
            Route::put('/eventos-rapidos/{rapido}', [CalendarioController::class, 'rapidosUpdate'])->name('rapidos.update');
            Route::delete('/eventos-rapidos/{rapido}', [CalendarioController::class, 'rapidosDestroy'])->name('rapidos.destroy');
        });
    });

    Route::prefix('accesos')->name('accesos.')->middleware('can:accesos.gestionar')->group(function () {
        Route::get('/', [AccesosController::class, 'index'])->name('index');
        Route::get('/roles', [AccesosController::class, 'roles'])->name('roles');
        Route::get('/{user}', [AccesosController::class, 'edit'])->name('edit');
        Route::put('/{user}/roles', [AccesosController::class, 'updateRoles'])->name('roles.update');
        Route::put('/{user}/permisos', [AccesosController::class, 'updatePermissions'])->name('permisos.update');
        Route::patch('/{user}/estado', [AccesosController::class, 'toggleActive'])->name('estado.update');
    });

    // Módulos del menú pendientes de desarrollo: la ruta ya queda protegida
    // por el permiso real que tendrá cada módulo cuando se implemente.
    $modulosPendientes = [
        ['uri' => 'tarjetas', 'name' => 'tarjetas.index', 'permission' => 'tarjetas.ver', 'titulo' => 'Control de Tarjetas'],
        ['uri' => 'vehiculos', 'name' => 'vehiculos.index', 'permission' => 'vehiculos.ver', 'titulo' => 'Vehículos Oficiales'],
        ['uri' => 'mantenimientos', 'name' => 'mantenimientos.index', 'permission' => 'mantenimientos.ver', 'titulo' => 'Mantenimientos'],
        ['uri' => 'kardex', 'name' => 'kardex.index', 'permission' => 'kardex.gestionar', 'titulo' => 'Ajustes Kárdex'],
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
