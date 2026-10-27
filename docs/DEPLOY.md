# Despliegue de AITROL

## Publicar sin `push` ni `pull`

El servidor puede quedar en la misma revisión de Git sin publicar el código en
un remoto. El método seguro es transportar un *bundle* de Git. El bundle solo
incluye cambios confirmados localmente; no incluye archivos modificados que aún
no tengan un commit.

En Windows, después de ejecutar las pruebas y crear el commit local:

```powershell
Set-Location C:\xampp\htdocs\aitrol_2026
git status --short
git bundle create C:\Temp\aitrol-main.bundle main
git bundle verify C:\Temp\aitrol-main.bundle
scp C:\Temp\aitrol-main.bundle administrador@192.168.66.10:/tmp/
```

Antes de actualizar el código, respalde la base y los archivos persistentes en
Linux. Estos comandos no eliminan ni sobrescriben la base:

```bash
cd /var/www/aitrol
mkdir -p /var/backups/aitrol
mysqldump --single-transaction --routines --triggers --events \
  -u root -p aitrol_respaldo_20261016 \
  > "/var/backups/aitrol/aitrol_$(date +%Y%m%d_%H%M%S).sql"
tar -czf "/var/backups/aitrol/storage_$(date +%Y%m%d_%H%M%S).tar.gz" storage .env
git status --short
```

Si `git status --short` muestra cambios en el servidor, deténgase y respáldelos;
no use `reset --hard`. Si está limpio, aplique el bundle mediante avance directo:

```bash
cd /var/www/aitrol
git bundle verify /tmp/aitrol-main.bundle
git fetch /tmp/aitrol-main.bundle main
git merge --ff-only FETCH_HEAD
git rev-parse HEAD
./deploy.sh --skip-pull
```

El script no ejecuta `git push`. Con `--skip-pull` tampoco consulta ni descarga
del remoto. `git diff --exit-code` permite comprobar que los archivos
versionados del servidor coinciden con la revisión aplicada.

## Base existente: `users` ya existe

AITROL adopta las tablas heredadas: las migraciones base consultan
`Schema::hasTable()` y solo crean lo que falta. Nunca ejecute `migrate:fresh`,
`migrate:reset` ni elimine manualmente `users` en esta base.

Compruebe primero que el servidor recibió la migración protegida:

```bash
cd /var/www/aitrol
grep -n "Schema::hasTable('users')" database/migrations/0001_01_01_000000_create_users_table.php
php artisan optimize:clear
php artisan config:show database.connections.mysql.database
php artisan migrate:status
php artisan migrate --pretend --force
```

La configuración debe indicar `aitrol_respaldo_20261016`. Si el `grep` no
devuelve una línea, el servidor aún tiene el archivo antiguo y no debe migrar.
Con el archivo correcto y el respaldo terminado:

```bash
php artisan migrate --force --isolated
php artisan optimize
```

La migración quedará registrada como ejecutada, pero las filas y la definición
de una tabla existente se conservan. Las pruebas automatizadas cubren la
reejecución y la preservación de datos centinela en `users`, `sessions`, `cache`
y `jobs`.

## Error `ViteManifestNotFoundException`

Este error significa que el servidor recibió las vistas nuevas, pero no se compiló el frontend. El archivo requerido es:

```text
/var/www/aitrol/public/build/manifest.json
```

Para reparar una publicación que ya está en el servidor:

```bash
cd /var/www/aitrol
npm ci
npm run build
php artisan optimize:clear
php artisan optimize
```

Después de publicar los nuevos scripts, puede ejecutar el flujo completo con `./deploy.sh`. El script comprueba que el manifiesto exista antes de ejecutar migraciones u optimizaciones.

## Linux

```bash
cd /var/www/aitrol
chmod +x deploy.sh
./deploy.sh
```

### Habilitar “Actualizar desde Git” sin sudo

El panel ejecuta `git pull --ff-only` directamente con el usuario de PHP. Configure una sola vez las ACL usando la cuenta propietaria del repositorio; el comando no utiliza `sudo`:

```bash
cd /var/www/aitrol
WEB_USER=apache PROJECT_PATH=/var/www/aitrol bash install-git-direct.sh
```

El instalador concede al usuario web escritura sobre los archivos versionados, sus directorios y `.git`; excluye `vendor` y `node_modules`, configura ACL predeterminadas para archivos futuros y mantiene `.env` como solo lectura. Las variables antiguas `DEPLOY_GIT_HELPER` y `DEPLOY_GIT_USER` se limpian automáticamente. No use `chmod 777`.

Este modo elimina la elevación de privilegios, pero también permite que el proceso web modifique el código. Mantenga el panel limitado a superadministradores, use autenticación fuerte y restrinja su acceso por red cuando sea posible.

Variables opcionales:

```bash
PHP_BIN=/usr/bin/php8.4 COMPOSER_BIN=/usr/local/bin/composer ./deploy.sh
```

Opciones disponibles:

```text
--skip-pull
--skip-composer
--skip-assets
--skip-migrations
--run-tests
--skip-tests
```

Las pruebas no se publican en Git y deben ejecutarse localmente antes de crear el
commit o bundle. Por eso el despliegue Linux omite las pruebas de forma
predeterminada. `--run-tests` solo funciona si el directorio local `tests/` fue
copiado expresamente al servidor para una validación temporal.

Ejemplo sin actualizar Git ni ejecutar migraciones:

```bash
./deploy.sh --skip-pull --skip-migrations
```

## Windows / XAMPP

```powershell
Set-Location C:\xampp\htdocs\aitrol_2026
.\deploy.ps1 `
    -Php 'C:\xampp\htdocs\borleti\.runtime\php\php.exe' `
    -Composer 'C:\ProgramData\ComposerSetup\bin\composer.phar'
```

Opciones disponibles:

```text
-SkipPull
-SkipComposer
-SkipAssets
-SkipMigrations
-RunTests
-SkipTests
```

En Windows las pruebas locales continúan ejecutándose de forma predeterminada.
Use `-SkipTests` únicamente cuando la misma revisión ya haya sido validada en
esa computadora.

## Fase interna de Laravel

El comando siguiente no actualiza Git, Composer ni npm. Está pensado para finalizar un release cuyos archivos ya están en el servidor:

```bash
php artisan app:deploy --force
```

Ejecuta, en orden:

1. Comprueba `public/build/manifest.json`.
2. Activa el modo mantenimiento, salvo que se use `--no-maintenance`.
3. Ejecuta `optimize:clear`.
4. Ejecuta `migrate --force --isolated`.
5. Ejecuta `optimize` para cachear configuración, eventos, rutas y vistas.
6. Ejecuta `queue:restart`.
7. Desactiva el modo mantenimiento si el comando lo activó.

Puede revisar el plan sin modificar nada:

```bash
php artisan app:deploy --dry-run
```

Opciones:

```text
--no-migrate
--no-optimize
--no-maintenance
--no-queue-restart
--no-asset-check
--dry-run
--force
```

## Recomendaciones de servidor

- El `DocumentRoot` de Nginx o Apache debe apuntar a `/var/www/aitrol/public`, nunca a la raíz del repositorio.
- El usuario del servidor web necesita escritura en `storage` y `bootstrap/cache`.
- El archivo `.env` de producción no debe estar en Git.
- Realice un respaldo de la base antes de migraciones importantes.
- Ejecute las pruebas localmente antes de crear el commit o bundle. El directorio `tests/` no se publica en Git.

### PHP 8.4 y rendimiento

En producción use `APP_ENV=production` y `APP_DEBUG=false`. `app:deploy` ya
ejecuta `php artisan optimize`, que cachea configuración, eventos, rutas y
vistas. Composer se instala con `--optimize-autoloader`; no se fuerza
`--classmap-authoritative` porque este proyecto conserva código legado y esa
opción puede impedir la carga de clases generadas dinámicamente.

Habilite OPcache en la configuración de PHP-FPM o Apache correspondiente a PHP
8.4, no únicamente en el `php.ini` del CLI. Un punto de partida conservador es:

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=32
opcache.max_accelerated_files=60000
opcache.validate_timestamps=1
opcache.revalidate_freq=2
realpath_cache_size=4096K
realpath_cache_ttl=600
```

Después de cambiar `php.ini`, reinicie PHP-FPM o Apache. Mantener
`opcache.validate_timestamps=1` evita servir código anterior si no se reinicia
PHP después de un despliegue. No habilite JIT como optimización predeterminada:
para una aplicación web Laravel el beneficio suele ser menor que OPcache y
aumenta el consumo de memoria. Redis para caché, sesiones o colas debe activarse
solo después de instalarlo, supervisarlo y probar el flujo completo; no cambie
los drivers de producción a ciegas.
