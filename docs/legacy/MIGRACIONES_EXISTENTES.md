# Migraciones sobre una base existente

## Migraciones anteriores de usuarios

Las tres migraciones históricas que agregan campos a `users` se recuperaron del
historial de Git y ahora comprueban `Schema::hasColumn()` por cada campo:

- `2015_10_25_000000_update_users_table.php`: nombres, apellidos, país, ciudad,
  institución y ocupación.
- `2016_07_07_225615_update_table_users_V2.php`: tipo de usuario.
- `2017_08_22_160719_update_table_users_V3.php`: cédula, teléfonos, fecha de
  nacimiento y estado.

Si un campo existe, conservan su definición y sus valores. Si falta, lo agregan.
No actualizan registros, no eliminan campos y sus métodos `down()` conservan los
datos. Los campos nuevos de la migración de 2017 admiten NULL para no inventar
identificaciones ni fechas en usuarios existentes; la cédula solo recibe un
índice único cuando se crea el campo. No dependen de las columnas antiguas
`imagenurl` o `telefono` para ubicar los nuevos campos.

Después de desplegar los archivos corregidos se puede usar el comando general:

```bash
cd /var/www/aitrol
php artisan migrate --force --isolated
```

Se mantienen los nombres y clases originales. Laravel reintenta las migraciones
pendientes y solo las registra al completarse. Una migración que ya figura como
ejecutada no se vuelve a ejecutar automáticamente.

Esta corrección cubre las tres migraciones de modificación de `users`, las
migraciones históricas de `pais`, las relaciones descritas a continuación y las migraciones actuales
incluidas en este repositorio; no implica haber revisado otros archivos que
existan únicamente en el servidor.

## Tabla de países

Se recuperaron y corrigieron las dos migraciones históricas de `pais`:

- `2016_03_26_211538_crear_tabla_pais.php`.
- `2017_08_31_143819_create_pais_table.php`.

Si la tabla no existe, cada migración conserva su definición original de
creación. Si existe, comprueba cada columna antes de agregarla. Se revisan
`id`, `nombre`, `created_at` y `updated_at`; la de 2017 también revisa `ip_crea`,
`ip_modifica` y `deleted_at`. Los timestamps se comprueban individualmente
para admitir tablas que solo tienen uno de ellos.

Los campos existentes conservan sus tipos, longitudes y valores, incluso
cuando son distintos de la definición histórica. Por ejemplo, la migración de
2017 no cambia la longitud de un `nombre` que ya existe. Si falta `nombre` en
una tabla existente, se agrega admitiendo NULL para conservar sus registros
sin asignarles nombres inventados. Los métodos `down()` conservan la tabla y
sus campos, porque pueden existir desde antes de estas migraciones.

## Claves foráneas de educación, publicaciones y proyectos

Las migraciones originales definían `idUsuario` como `INT UNSIGNED`, mientras
que la definición histórica de `users.id` utiliza texto. La nueva versión
consulta la definición real de `users.id` en la conexión de la aplicación y
crea `idUsuario` con un tipo compatible, conservando tamaño, signo, juego de
caracteres y collation. Las tablas nuevas utilizan InnoDB.

Se corrigieron estos archivos y se incluyeron en Git junto con el helper
`app/Support/Migrations/UserForeignKey.php`:

- `2016_03_13_151018_crear_tabla_educacion.php`.
- `2016_03_20_230021_crear_tabla_publicaciones.php`.
- `2016_03_25_161404_crear_tabla_proyectos.php`.

El error de clave foránea puede dejar la tabla creada antes de fallar. Al
reintentar, se utiliza esa misma tabla. Si está vacía y conserva la definición
simple original, se adapta `idUsuario` y se agrega la relación pendiente.
Una tabla con campos compatibles conserva sus definiciones y registros; una
clave foránea existente hacia `users.id` se conserva, incluso con otro nombre.
No se modifican `users.id` ni sus datos. Los métodos `down()` de estas tres
migraciones conservan sus tablas.

Si la tabla contiene registros y necesita una conversión de tipo o motor, la
migración se detiene antes de convertirlos. También se detiene si hay referencias
a usuarios inexistentes, una relación diferente sobre `idUsuario` o una
definición personalizada que no pueda adaptar con seguridad. No borra,
reidentifica ni reasigna esos registros y Laravel no registra la migración como
terminada. No se desactivan las comprobaciones de claves foráneas.

Para diagnosticar una tabla con datos que requiere revisión, estas consultas
solo leen su estructura y cantidad de registros:

```sql
SHOW FULL COLUMNS FROM users LIKE 'id';
SHOW FULL COLUMNS FROM educacion LIKE 'idUsuario';
SELECT COUNT(*) AS registros FROM educacion;
```

La compatibilidad de tipos, signo y codificación sigue las reglas de
[claves foráneas de MySQL](https://dev.mysql.com/doc/refman/8.0/en/create-table-foreign-keys.html).

## Migraciones actuales

Las migraciones base `0001_01_01_000000`, `0001_01_01_000001` y
`0001_01_01_000002` también adoptan tablas existentes. Antes de cada creación
consultan `Schema::hasTable()` para `users`, `password_reset_tokens`,
`sessions`, `cache`, `cache_locks`, `jobs`, `job_batches` y `failed_jobs`. Sus
métodos `down()` no eliminan estas tablas compartidas con el sistema legado.

Si aparece `Table 'users' already exists`, el servidor está ejecutando una
versión anterior de `0001_01_01_000000_create_users_table.php`. Actualice ese
archivo antes de reintentar; no borre `users` ni inserte manualmente registros
en la tabla `migrations`.

Las migraciones actuales (`2026_*.php`) de creación de tablas consultan `Schema::hasTable()` antes de
crearlas. Las que agregan columnas consultan `Schema::hasColumn()` por cada
columna, para completar una ejecución parcial sin modificar las columnas que ya
existen. Los índices agregados a tablas existentes también se consultan por
nombre antes de crearlos.

Estas comprobaciones corresponden a `up()`, utilizado por el ejecutor de Laravel.
No sincronizan toda la estructura de una tabla existente ni ocultan otros
errores de SQL. Las migraciones que convierten tipos conservan su propósito
original; los métodos `down()` siguen teniendo operaciones de eliminación.

Cada campo agregado a una tabla existente usa esta validación:

```php
Schema::table('ct_entregas_mercaderia_detalle', function (Blueprint $table) {
    if (!Schema::hasColumn('ct_entregas_mercaderia_detalle', 'precio_venta')) {
        $table->decimal('precio_venta', 14, 6)->default(0)->after('cantidad');
    }
});
```

## Pruebas de regresión

`tests/Integration/MigrationIdempotencyTest.php` carga y ejecuta las
migraciones históricas corregidas y las actuales (`2026_*.php`) con
Illuminate sobre MySQL/MariaDB. Comprueba instalación nueva, reejecución sin
duplicados, tablas y columnas creadas parcialmente, índices faltantes, prefijos
de tablas y conservación de registros existentes. Cada prueba crea y elimina
una base con nombre aleatorio `migration_test_*`.

Para las migraciones históricas, prueba usuarios con solo `ciudad`, con algunos
campos existentes, con todos y sin ninguno de los campos requeridos. Verifica
que se conserven definiciones y datos incluso con cédulas duplicadas que ya
existían. También ejecuta el comando estándar de Laravel con migraciones antiguas
y actuales pendientes, y comprueba una segunda ejecución sin duplicados.

Para `pais`, prueba ambas creaciones desde cero y tablas existentes completas
o parciales. Comprueba la conservación de identificadores, nombres largos,
fechas, direcciones IP, campos adicionales y definiciones personalizadas.
Verifica que las reejecuciones no emitan escrituras y que el comando estándar
complete las dos migraciones pendientes sobre la tabla original con datos.

Para las claves foráneas, reproduce el error 1005/150 y recupera la tabla vacía.
Prueba referencias VARCHAR y CHAR con una collation diferente a la conexión,
enteros con signo y sin signo, prefijos de tabla y conservación de filas y
definiciones existentes. Verifica que los registros incompatibles o huérfanos
produzcan un error sin ser convertidos, borrados ni reasignados.

Utilizar un servidor exclusivo para pruebas y un usuario con permisos para
crear esas bases. La prueba no carga `.env` ni la configuración de producción:

```bash
MIGRATION_TEST_MYSQL_PORT=33319 php vendor/bin/phpunit --no-configuration \
  --bootstrap vendor/autoload.php tests/Integration/MigrationIdempotencyTest.php
```

Opcionalmente se pueden definir `MIGRATION_TEST_MYSQL_HOST`,
`MIGRATION_TEST_MYSQL_USER` y `MIGRATION_TEST_MYSQL_PASSWORD`. Sin el puerto
explícito, las pruebas se omiten.
