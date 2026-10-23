# AITROL 2026

Migración estructural del sistema **Sucursales IECED** a Laravel 13. El objetivo es conservar sus URLs, reglas de negocio, vistas y módulos, usando una base moderna que permita estabilizarlos y refactorizarlos gradualmente.

## Estado actual

- Laravel 13.38 sobre PHP 8.4.
- 5.510 rutas registradas por la aplicación.
- 1.613 archivos PHP del núcleo funcional integrados en `app/SisMedico`.
- 3.199 vistas Blade y los recursos públicos originales preservados.
- Compatibilidad para Laravel Collective, DomPDF, códigos de barras, FFmpeg, Intervention Image, Google API y Redis.
- Adaptador propio para ejecutar reportes escritos con la API antigua de Laravel Excel/PHPExcel sobre PhpSpreadsheet 5.
- Catálogo visual de 34 módulos en `/mapa-modulos`.

El código original en `C:\xampp7.4\htdocs\sucursales_ieced` no fue modificado.

## Requisitos

- PHP 8.3 o superior con las extensiones requeridas por Laravel y por MySQL.
- Composer 2.
- Node.js y npm.
- La base de datos del sistema Sucursales IECED. Sus 21 migraciones históricas no representan el esquema completo.

El PHP principal de `C:\xampp\php\php.exe` es 8.2 y no puede ejecutar Laravel 13. En este equipo el proyecto fue validado con:

```powershell
C:\xampp\htdocs\borleti\.runtime\php\php.exe
```

## Instalación local

```powershell
Copy-Item .env.example .env
& 'C:\xampp\htdocs\borleti\.runtime\php\php.exe' 'C:\ProgramData\ComposerSetup\bin\composer.phar' install
& 'C:\xampp\htdocs\borleti\.runtime\php\php.exe' artisan key:generate
npm install
npm run build
```

Configure en `.env` la conexión MySQL y las integraciones. No se copiaron contraseñas del proyecto anterior.

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aitrol
DB_USERNAME=root
DB_PASSWORD=
```

Inicie la aplicación:

```powershell
& 'C:\xampp\htdocs\borleti\.runtime\php\php.exe' artisan serve
```

Abra `http://127.0.0.1:8000/mapa-modulos`.

## Despliegue

El proyecto incluye un despliegue completo para Windows y Linux. Ambos activan mantenimiento, actualizan el repositorio, instalan dependencias, compilan Vite, verifican `public/build/manifest.json`, ejecutan migraciones, optimizan Laravel, reinician las colas y vuelven a habilitar el sitio aunque ocurra un error.

Windows:

```powershell
.\deploy.ps1 -Php 'C:\ruta\php.exe' -Composer 'C:\ruta\composer.phar'
```

Linux:

```bash
chmod +x deploy.sh
./deploy.sh
```

Para ejecutar solamente la fase Laravel:

```bash
php artisan app:deploy --force
```

Consulte todas las opciones y la solución al error de manifiesto Vite en [docs/DEPLOY.md](docs/DEPLOY.md).

## Validación

```powershell
& 'C:\xampp\htdocs\borleti\.runtime\php\php.exe' artisan test --compact
& 'C:\xampp\htdocs\borleti\.runtime\php\php.exe' vendor\bin\pint
npm run build
```

La estrategia, los límites del código fuente recibido y los pasos de puesta en producción están documentados en [docs/MIGRACION_LARAVEL_13.md](docs/MIGRACION_LARAVEL_13.md).
