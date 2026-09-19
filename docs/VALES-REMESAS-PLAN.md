# Módulo: Control de Vales y Remesas COMETRA (dentro de SIOS)

## Contexto

- Repo: armocte18-alt/sies (sistema SIOS, Laravel 13, ya en producción).
- Rama de trabajo: `claude/wizardly-mccarthy-xhbwlv`
- Ruta local correcta: `D:\Servidor\www\sies` (no usar `mercomuna`,
  `sios-app-web` u otras carpetas locales — verificar con `git remote -v`
  cuál apunta a `armocte18-alt/sies` antes de trabajar).
- Origen del requerimiento: las sucursales reciben vales Mercomuna de
  personas afiliadas, arman paquetes al cierre del día, y Coordinación de
  Operación sube semanalmente un Excel consolidado (formato
  "Registro_Remesas_Vales_Cometra") con el detalle. COMETRA (empresa de
  traslado de valores) recoge esos paquetes agrupados en "remesas".

## Decisión de arquitectura

No es un proyecto nuevo. Se integra como módulo dentro de SIOS,
reutilizando: autenticación (Breeze), roles/permisos
(`spatie/laravel-permission`), layout/sidebar, y la tabla `sucursales` ya
existente (coincide por `clave_financiera`, ej. `09005`).

Stack (sin cambios respecto a SIOS): Laravel 13 + Blade + Tailwind + Alpine.js
+ MySQL 8. Única librería nueva: `maatwebsite/excel` (importar el Excel
semanal).

## Reglas de negocio confirmadas

- Captura: solo vía Excel semanal subido por Coordinación de Operación. No
  hay formulario web de captura diaria por sucursal.
- Sucursal del Excel = `sucursales.clave_financiera` (match directo).
- Estatus de paquete: Preparado → En Tránsito → Recibido por Cometra. Al
  asignar paquetes "Preparado" a una remesa, todos pasan juntos a "En
  Tránsito"; al confirmar entrega, todos pasan a "Recibido por Cometra".

## Modelo de datos (3 tablas nuevas)

### `vale_paquetes`

- `sucursal_id` (FK `sucursales`)
- `fecha_paquete` (date)
- `numero_paquete` (string, único por sucursal+fecha)
- `vales_50`, `vales_100`, `vales_anomalos_50`, `vales_anomalos_100` (unsignedInteger)
- `estatus` (enum: `preparado`, `en_transito`, `recibido_cometra`)
- `observaciones` (text, nullable)
- `remesa_id` (FK `vale_remesas`, nullable)
- `reporte_semanal_id` (FK `vale_reportes_semanales`, nullable)
- `capturado_por`, `created_by`, `updated_by` (FK `users`)
- Accessors calculados (no se guardan en BD, igual que `SucursalFinanza::balance`):
  - `totalVales()` = `vales_50 + vales_100 + vales_anomalos_50 + vales_anomalos_100`
  - `montoTotal()` = `(vales_50 + vales_anomalos_50) * 50 + (vales_100 + vales_anomalos_100) * 100`

### `vale_remesas`

- `numero_remesa` (string, único)
- `numero_ficha_cometra` (string, único)
- `fecha_entrega` (date)
- `created_by` (FK `users`)

### `vale_reportes_semanales`

- `semana_inicio`, `semana_fin` (date)
- `archivo_original` (string, ruta en storage)
- `subido_por` (FK `users`)
- `filas_procesadas`, `filas_con_error` (unsignedInteger)
- `estatus` (enum: `procesado`, `con_errores`)

## Permisos nuevos (spatie, sobre rol `operacion` ya existente)

- `vales.capturar` (reservado, no se usa en MVP sin captura web)
- `vales.importar` — subir Excel semanal
- `vales.asignar_remesa` — agrupar paquetes y marcarlos recogidos por COMETRA
- `vales.ver_estadisticas` — dashboard y listados (sucursal ve solo lo suyo)

## Pantallas (Fase 2 — en validación)

1. Sidebar: nueva sección "Vales y Remesas".
2. Importar reporte semanal (Excel + previsualización + validación de
   errores antes de guardar).
3. Historial de reportes semanales (auditoría: quién subió, cuándo,
   errores).
4. Control de Paquetes (tabla filtrable: sucursal, fecha, estatus, remesa).
5. Detalle de Paquete (solo lectura, todo viene del Excel).
6. Remesas COMETRA (agrupar paquetes "Preparado" → asignar remesa → marcar
   entregado).
7. Dashboard estadístico (KPIs + gráficas por sucursal/semana + mapa
   Leaflet reutilizado, coloreado por volumen de vales).

## Siguiente paso (Fase 3 — pendiente de arrancar)

Con Fase 1 (BD) validada y Fase 2 (pantallas) en revisión, el orden de
implementación será:

1. Migraciones + modelos + factories.
2. Seeder de permisos nuevos.
3. Importador de Excel (`maatwebsite/excel`) con validación fila por fila.
4. Controladores + FormRequests + Policies.
5. Vistas Blade (listados, detalle, dashboard).
6. Pruebas automatizadas (Feature tests, siguiendo el patrón de
   `tests/Feature/Sucursales/`).

## Cómo seguir en VS Code / Chrome

- Esta sesión: https://claude.ai/code/session_011jSy8TyMBq2sPEsFa8vWnA
- Traer avances a tu máquina:
  ```powershell
  git fetch origin claude/wizardly-mccarthy-xhbwlv
  git checkout claude/wizardly-mccarthy-xhbwlv
  composer install
  php artisan migrate
  php artisan serve
  ```
  Revisar en tu Chrome local: `http://127.0.0.1:8000`
