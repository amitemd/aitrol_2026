# Despliegue de AITROL

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

Las pruebas se ejecutan de forma predeterminada. `--skip-tests` debe usarse únicamente cuando la misma revisión ya haya pasado en CI.

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

Las pruebas se ejecutan de forma predeterminada. `-SkipTests` debe usarse únicamente cuando la misma revisión ya haya pasado en CI.

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
- Ejecute las pruebas en CI o use `--run-tests` durante el despliegue cuando el servidor tenga dependencias de desarrollo.
