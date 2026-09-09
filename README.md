# SIES · Sistema de Información, Estadística y Servicios

Desarrollado por la Coordinación de Operación GECDMX · V.1.0.0

Plataforma de gestión para la Gerencia Estatal FINABIEN Ciudad de México y sus
coordinaciones (Supervisión, Operación, Créditos, Jurídico, Finanzas,
Administración, Técnica, Recursos Humanos y Comercial): sucursales, empleados,
directorios, minutarios, circulares y demás módulos operativos.

La arquitectura completa, el modelo de datos y el plan de desarrollo por fases
están en [`docs/ARQUITECTURA.md`](docs/ARQUITECTURA.md). Este documento cubre
la instalación local en Windows con Apache 2.4 y MySQL.

## Requisitos

- PHP 8.3+ con las extensiones habituales de Laravel (`mbstring`, `pdo_mysql`,
  `openssl`, `fileinfo`, `curl`) — vienen activadas en XAMPP/Laragon.
- Composer 2
- Node.js 20+ y npm
- MySQL 8 (o MariaDB 10.6+)
- Apache 2.4 con `mod_rewrite` y `mod_headers` habilitados

## Instalación

Clonar el repositorio directamente en la ruta del servidor:

```powershell
cd D:\Servidor\www
git clone https://github.com/armocte18-alt/sies.git sies
cd sies
```

Dependencias e infraestructura de la app:

```powershell
composer install
copy .env.example .env
php artisan key:generate
npm install
npm run build
```

Editar `.env` con los datos reales de MySQL y del primer administrador:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sies
DB_USERNAME=sies_app
DB_PASSWORD=una-contraseña-fuerte

SIES_ADMIN_EMAIL=admin@finabien.cdmx.gob.mx
SIES_ADMIN_PASSWORD=defínela-aquí-antes-de-sembrar
```

Crear la base de datos vacía en MySQL (`CREATE DATABASE sies CHARACTER SET
utf8mb4;`) y luego:

```powershell
php artisan migrate --seed
```

Esto crea los catálogos (coordinaciones, alcaldías), los roles/permisos y el
usuario administrador. **Cambia esa contraseña temporal en cuanto inicies
sesión.**

## Apache 2.4 — VirtualHost para intranet

Habilita los módulos necesarios en `httpd.conf` (`mod_rewrite`,
`mod_headers`) y agrega un VirtualHost apuntando al `public/` de Laravel — nunca
a la raíz del proyecto:

```apacheconf
<VirtualHost *:80>
    ServerName sies-gecdmx.com
    ServerAlias www.sies-gecdmx.com sies-gecdmx.com.<IP-DEL-SERVIDOR>.nip.io
    DocumentRoot "D:/Servidor/www/sies/public"

    <Directory "D:/Servidor/www/sies/public">
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "logs/sies-gecdmx-error.log"
    CustomLog "logs/sies-gecdmx-access.log" combined
</VirtualHost>
```

Para que las demás computadoras de la intranet resuelvan `sies-gecdmx.com`,
agrega una entrada en el DNS interno o en el archivo `hosts` de cada equipo
apuntando a la IP del servidor (o usa el alias `nip.io` de arriba, que no
requiere tocar el `hosts` de cada máquina). Con `AllowOverride All`, Laravel usa el
`.htaccess` que ya viene en `public/` para las URLs amigables — confirma que
`mod_rewrite` esté cargado.

Con `APP_ENV=production` (valor por defecto en `.env.example`) recuerda:

```powershell
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

y volver a ejecutarlos cada vez que actualices `.env`, rutas o vistas en
producción.

## Actualizar el proyecto (flujo con GitHub)

```powershell
cd D:\Servidor\www\sies
git pull origin main
composer install --no-dev
npm install
npm run build
php artisan migrate
php artisan config:clear
```

## Desarrollo local

```powershell
php artisan serve
npm run dev
```

## Pruebas

```powershell
php artisan test
```

## Estructura del proyecto

Ver [`docs/ARQUITECTURA.md`](docs/ARQUITECTURA.md#3-estructura-de-carpetas).
