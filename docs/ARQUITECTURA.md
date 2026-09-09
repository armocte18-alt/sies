# Arquitectura técnica — SIOS (Sistema Integral de Operación y Servicios)

Plataforma institucional para la Gerencia Estatal FINABIEN Ciudad de México y sus
coordinaciones (Supervisión, Operación, Créditos, Jurídico, Finanzas,
Administración, Técnica, Recursos Humanos y Comercial).

## 1. Tecnologías

| Capa | Tecnología | Motivo |
|---|---|---|
| Backend | Laravel 13 (PHP 8.3+) | Framework maduro, ORM Eloquent, autorización/validación integradas, ecosistema grande en español, fácil de alojar en Apache/Windows |
| Frontend | Blade + Tailwind CSS (Vite) + Alpine.js | Renderizado en servidor (sin SPA), rápido de asegurar, Alpine cubre interactividad (tabs, menús, mapa) sin un framework JS completo |
| Mapa | Leaflet + OpenStreetMap | Sin costo ni API key, suficiente para ubicar sucursales |
| Base de datos | MySQL 8 | Requisito del proyecto; motor estándar en hosting compartido/Windows (XAMPP/Laragon) |
| Autenticación | Laravel Breeze (stack Blade) | Login, registro, verificación de correo y recuperación de contraseña listos y auditados |
| Autorización | spatie/laravel-permission | Roles = coordinaciones, permisos granulares por módulo y por sección dentro de un módulo |
| Servidor web | Apache 2.4 sobre Windows | Definido por el usuario: despliegue en intranet vía VirtualHost, `DocumentRoot` → `public/` |

## 2. Arquitectura por capas

```
Request HTTP
   │
   ▼
routes/web.php ──► middleware (auth, verified, can:permiso)
   │
   ▼
Controller (delgado)
   │
   ├─► FormRequest  → valida datos + autoriza (Policy) antes de llegar al controlador
   ├─► Policy        → decide si el usuario puede ver/editar según su rol/coordinación
   ▼
Eloquent Model ──► Base de datos MySQL
   │
   ▼
Blade View (components reutilizables) ──► HTML + Tailwind + Alpine.js
```

Principios aplicados:

- **Controladores delgados**: la validación vive en `FormRequest`, la autorización en
  `Policy`, el controlador solo orquesta.
- **Un modelo por tabla, con relaciones explícitas** (`hasOne`, `belongsTo`, etc.),
  sin lógica de negocio en las vistas.
- **Componentes Blade** (`resources/views/components`) para todo lo repetido: layout,
  tarjetas de métricas, badges de estatus, iconos.
- **Permisos por sección, no solo por módulo**: una sucursal la editan varias
  coordinaciones a la vez, cada una solo en su parte (ver §4).

## 3. Estructura de carpetas

```
sies/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── SucursalController.php        # index/show/create/store + cédula
│   │   │   ├── DashboardController.php
│   │   │   ├── PlaceholderController.php     # módulos aún no implementados
│   │   │   └── Sucursales/                   # un controlador por sección/coordinación
│   │   │       ├── UbicacionController.php
│   │   │       ├── HorariosController.php
│   │   │       ├── InmuebleController.php
│   │   │       ├── EquipamientoController.php
│   │   │       └── FinanzasController.php
│   │   ├── Requests/Sucursales/              # 1 FormRequest por sección (validación + authorize())
│   │   └── Middleware/SecurityHeaders.php
│   ├── Models/                               # Sucursal + 5 modelos "sección" + catálogos
│   └── Policies/SucursalPolicy.php
├── config/sios.php                           # navegación del sidebar y permisos asociados
├── database/
│   ├── migrations/                           # una tabla por sección de sucursal
│   ├── seeders/                              # coordinaciones, alcaldías, roles/permisos, admin
│   └── factories/
├── resources/
│   ├── views/
│   │   ├── components/                       # layout, iconos, tarjetas, badges
│   │   ├── sucursales/                       # index, create, show + partials por sección
│   │   ├── dashboard.blade.php
│   │   └── modules/placeholder.blade.php
│   ├── css/app.css
│   └── js/{app.js,sucursales-map.js}
├── routes/web.php
├── docs/ARQUITECTURA.md                      # este documento
└── tests/{Feature,Unit}
```

## 4. Módulo de Sucursales: una tabla por coordinación

El requisito central del módulo es que **varias coordinaciones alimentan la misma
sucursal, cada una solo su parte**. Esto se modela con una tabla `sucursales`
(identidad) y una tabla satélite `1 a 1` por coordinación:

| Tabla | Coordinación responsable | Contenido |
|---|---|---|
| `sucursales` | Operación | Nombre oficial, clave financiera, estatus, titular, centro de distribución |
| `sucursal_ubicaciones` | Operación | Calle, colonia, alcaldía, CP, referencias, lat/long, clave INEGI |
| `sucursal_operaciones` | Operación | Horarios público/interno, tipo de población, comunicación, guardias, reparto |
| `sucursal_inmuebles` | Administración | Tipo de contrato de posesión, superficie, medidas, fechas y monto de renta |
| `sucursal_equipamientos` + `activos_ti` | Técnica | Cómputo, impresoras, servidores, cámaras/alarma/extintores, enlace de datos |
| `sucursal_finanzas` | Finanzas | Límite de caja, volumen, cantidad situada, ingreso estimado, gasto total |
| `empleados` | RR.HH. / Operación | Plantilla adscrita a cada sucursal (`sucursal_id`) |

El balance y el estatus financiero (`↑ Superavitaria` / `↓ Deficitaria`) **se
calculan**, no se guardan (`SucursalFinanza::balance` / `estatus_financiero`),
para que nunca queden desincronizados del ingreso/gasto capturado.

### Autorización granular

`spatie/laravel-permission` modela **roles = coordinaciones** (`operacion`,
`administracion`, `tecnica`, `finanzas`, `rrhh`, …) y permisos como
`sucursales.editar.finanzas`, `sucursales.editar.equipamiento`, etc.
(`database/seeders/RolesAndPermissionsSeeder.php`). Cada sección de la vista de
detalle de una sucursal decide con `@can('updateX', $sucursal)` si muestra el
formulario editable o solo lectura — verificado con pruebas automatizadas
(`tests/Feature/Sucursales/SucursalPermissionsTest.php`): Técnica puede editar
equipamiento pero recibe 403 al intentar tocar finanzas, y viceversa.

## 5. Seguridad implementada

- **CSRF**: token en cada formulario (`@csrf`), verificado por el middleware
  estándar de Laravel.
- **Mass assignment**: cada modelo declara sus campos permitidos con el atributo
  `#[Fillable([...])]` (whitelist explícita, nunca `$guarded = []`).
- **SQL injection**: toda consulta pasa por Eloquent/Query Builder con bindings
  parametrizados; no hay SQL crudo con datos de usuario interpolados.
- **XSS**: Blade escapa por defecto (`{{ }}`); el único contenido dinámico
  insertado como HTML (popups del mapa) se escapa manualmente en
  `resources/js/sucursales-map.js`.
- **Autorización en profundidad**: Policy (`SucursalPolicy`) + `authorize()` en
  cada `FormRequest` + middleware `can:` en las rutas de módulos futuros — nunca
  solo ocultar el botón en la vista.
- **Contraseñas**: política reforzada (`Password::min(10)->mixedCase()->numbers()->symbols()`
  en `AppServiceProvider`), hash con bcrypt, límite de intentos de login (5) ya
  incluido por Laravel Breeze.
- **Cabeceras de seguridad**: `App\Http\Middleware\SecurityHeaders` agrega
  `Content-Security-Policy`, `X-Frame-Options: DENY`, `X-Content-Type-Options:
  nosniff`, `Referrer-Policy` y `Permissions-Policy` a toda respuesta.
- **Soft deletes** en `sucursales` y `empleados`: una baja no borra el histórico.

## 6. Plan de desarrollo por fases

**Fase 0 — Cimientos (entregada en esta iteración)**
Autenticación, roles/permisos por coordinación, layout con sidebar, dashboard con
KPIs y mapa, y el módulo de **Sucursales** completo (identificación, ubicación,
horarios/operación, inmueble, equipamiento técnico, finanzas), con pruebas
automatizadas.

**Fase 1 — Empleados y Directorios**
Alta/baja/cambios de personal ligado a `empleados`, kárdex básico, directorio de
contactos internos y de sucursales.

**Fase 2 — Minutarios y Circulares**
Bitácora de acuerdos/minutas por coordinación y publicación de circulares con
acuse de lectura.

**Fase 3 — Calendario de Eventos y Control de Tarjetas**
Agenda institucional (alimenta la tarjeta "Eventos próximos" del dashboard) y
control de tarjetas de acceso/gasto.

**Fase 4 — Vehículos Oficiales y Mantenimientos**
Resguardo vehicular y bitácora de mantenimientos (de vehículos e inmuebles,
ligada a `sucursal_inmuebles`).

**Fase 5 — Productividad y Ajustes RR.HH.**
"Metas y Análisis" (indicadores por sucursal/coordinación) y "Ajustes Kárdex".

**Fase 6 — Control de Accesos (UI)**
Hoy los roles/permisos se administran por seeder/tinker; esta fase construye la
pantalla para que un administrador cree usuarios, asigne rol/coordinación y
active/desactive cuentas sin tocar código.

**Fase 7 — Explotación de datos**
Reportes ejecutivos exportables (Excel/PDF), notificaciones internas y refinar
el dashboard con series históricas.

## 7. Despliegue en intranet (Windows + Apache 2.4 + MySQL)

Ver `README.md` para los pasos de instalación y el ejemplo de `VirtualHost`.
