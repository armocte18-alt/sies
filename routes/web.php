<?php

use App\Http\Controllers\AccesosController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\CatalogosRhController;
use App\Http\Controllers\CircularController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\MetasAnalisisController;
use App\Http\Controllers\MinutarioController;
use App\Http\Controllers\TarjetaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DirectorioController;
use App\Http\Controllers\PlaceholderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\Sucursales\DocumentoAcervoController;
use App\Http\Controllers\Sucursales\EquipamientoController;
use App\Http\Controllers\Sucursales\FinanzasController;
use App\Http\Controllers\Sucursales\HorariosController;
use App\Http\Controllers\Sucursales\InmuebleController;
use App\Http\Controllers\Sucursales\RepartoController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\Sucursales\UbicacionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('sucursales')->name('sucursales.')->group(function () {
        Route::get('/', [SucursalController::class, 'index'])->name('index');
        Route::get('/exportar/excel', [SucursalController::class, 'exportarExcel'])->name('exportar.excel');
        Route::get('/exportar/pdf', [SucursalController::class, 'exportarPdf'])->name('exportar.pdf');
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

        Route::post('/{sucursal}/acervo', [DocumentoAcervoController::class, 'store'])->name('acervo.store');
        Route::post('/acervo/tipos', [DocumentoAcervoController::class, 'storeTipo'])->name('acervo.tipos.store');
        Route::get('/acervo/versiones/{version}/descargar', [DocumentoAcervoController::class, 'descargar'])->name('acervo.versiones.descargar');
        Route::patch('/acervo/versiones/{version}/restaurar', [DocumentoAcervoController::class, 'restaurarVersion'])->name('acervo.versiones.restaurar');
        Route::delete('/acervo/versiones/{version}', [DocumentoAcervoController::class, 'eliminarVersion'])->name('acervo.versiones.destroy');
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

    Route::prefix('tarjetas')->name('tarjetas.')->middleware('can:tarjetas.ver')->group(function () {
        Route::get('/', [TarjetaController::class, 'index'])->name('index');
        Route::get('/historial', [TarjetaController::class, 'historial'])->name('historial');

        Route::middleware('can:tarjetas.gestionar')->group(function () {
            Route::post('/productos', [TarjetaController::class, 'storeProducto'])->name('productos.store');
            Route::post('/', [TarjetaController::class, 'storeTarjeta'])->name('store');
            Route::post('/asignar', [TarjetaController::class, 'asignar'])->name('asignar');
            Route::patch('/{tarjeta}/retirar', [TarjetaController::class, 'retirar'])->name('retirar');
            Route::post('/retirar-por-sucursal', [TarjetaController::class, 'retirarPorSucursal'])->name('retirar-por-sucursal');
            Route::patch('/{tarjeta}/renominar', [TarjetaController::class, 'renominar'])->name('renominar');
            Route::patch('/{tarjeta}/corregir-renominacion', [TarjetaController::class, 'corregirRenominacion'])->name('corregir-renominacion');
        });
    });

    Route::prefix('vehiculos')->name('vehiculos.')->middleware('can:vehiculos.ver')->group(function () {
        Route::get('/', [VehiculoController::class, 'index'])->name('index');
        Route::get('/km-sugerido/{vehiculo}', [VehiculoController::class, 'kmSugerido'])->name('km-sugerido');
        Route::get('/solicitudes/{solicitud}/responsiva', [VehiculoController::class, 'responsivaPdf'])->name('solicitudes.responsiva');

        Route::middleware('can:vehiculos.gestionar')->group(function () {
            Route::post('/', [VehiculoController::class, 'storeVehiculo'])->name('store');
            Route::put('/{vehiculo}', [VehiculoController::class, 'updateVehiculo'])->name('update');

            Route::post('/conductores', [VehiculoController::class, 'storeConductor'])->name('conductores.store');
            Route::put('/conductores/{conductor}', [VehiculoController::class, 'updateConductor'])->name('conductores.update');

            Route::post('/solicitudes', [VehiculoController::class, 'storeSolicitud'])->name('solicitudes.store');
            Route::patch('/solicitudes/{solicitud}/autorizar', [VehiculoController::class, 'autorizarSolicitud'])->name('solicitudes.autorizar');
            Route::patch('/solicitudes/{solicitud}/rechazar', [VehiculoController::class, 'rechazarSolicitud'])->name('solicitudes.rechazar');
            Route::patch('/solicitudes/{solicitud}/devolucion', [VehiculoController::class, 'devolucionSolicitud'])->name('solicitudes.devolucion');
        });
    });

    Route::prefix('mantenimientos')->name('mantenimientos.')->middleware('can:mantenimientos.ver')->group(function () {
        Route::get('/', [MantenimientoController::class, 'index'])->name('index');

        Route::middleware('can:mantenimientos.gestionar')->group(function () {
            Route::post('/', [MantenimientoController::class, 'store'])->name('store');
            Route::put('/{mantenimiento}', [MantenimientoController::class, 'update'])->name('update');
            Route::delete('/{mantenimiento}', [MantenimientoController::class, 'destroy'])->name('destroy');
            Route::patch('/{mantenimiento}/estatus', [MantenimientoController::class, 'cambiarEstatus'])->name('estatus');

            Route::post('/{mantenimiento}/materiales', [MantenimientoController::class, 'storeMaterial'])->name('materiales.store');
            Route::delete('/{mantenimiento}/materiales/{material}', [MantenimientoController::class, 'destroyMaterial'])->name('materiales.destroy');

            Route::post('/{mantenimiento}/personal', [MantenimientoController::class, 'storePersonal'])->name('personal.store');
            Route::delete('/{mantenimiento}/personal/{empleado}', [MantenimientoController::class, 'destroyPersonal'])->name('personal.destroy');

            Route::post('/tipos', [MantenimientoController::class, 'storeTipo'])->name('tipos.store');
        });
    });

    Route::prefix('accesos')->name('accesos.')->middleware('can:accesos.gestionar')->group(function () {
        Route::get('/', [AccesosController::class, 'index'])->name('index');
        Route::get('/roles', [AccesosController::class, 'roles'])->name('roles');
        Route::post('/roles', [AccesosController::class, 'storeRole'])->name('roles.store');
        Route::delete('/roles/{role}', [AccesosController::class, 'destroyRole'])->name('roles.destroy');
        Route::put('/roles/{role}/permisos', [AccesosController::class, 'updateRolePermissions'])->name('roles.permisos.update');
        Route::post('/permisos', [AccesosController::class, 'storePermission'])->name('permisos.store');
        Route::get('/{user}', [AccesosController::class, 'edit'])->name('edit');
        Route::put('/{user}/roles', [AccesosController::class, 'updateRoles'])->name('roles.update');
        Route::put('/{user}/permisos', [AccesosController::class, 'updatePermissions'])->name('permisos.update');
        Route::patch('/{user}/estado', [AccesosController::class, 'toggleActive'])->name('estado.update');
    });

    // Módulos del menú pendientes de desarrollo: la ruta ya queda protegida
    // por el permiso real que tendrá cada módulo cuando se implemente.
    $modulosPendientes = [
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
