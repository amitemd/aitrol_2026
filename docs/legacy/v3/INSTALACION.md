# Instalacion y ambientes de v3

## Modelo confirmado

El repositorio es una copia de desarrollo. `.gitignore` excluye intencionalmente `.env`, `composer.json`, `composer.lock`, `vendor`, `bootstrap`, `database`, `public` y `storage`. Estos elementos existen y se administran directamente en el servidor.

La ausencia de esos archivos en Git no indica una instalacion incompleta. Esta guia documenta como proteger la separacion entre el codigo versionado y los componentes operativos del servidor.

## Prerequisitos conocidos

- Servidor web compatible con Laravel.
- PHP 7.1.33 y Laravel 5.4, que son las versiones utilizadas por el servidor.
- Extensiones PHP requeridas por Laravel, MySQL, XML, firma electronica y generacion de documentos.
- Servidor MySQL con el esquema y datos maestros del sistema.
- Permisos de escritura para cache, sesiones y logs.
- Recursos web de v3 (AdminLTE, JavaScript, CSS e imagenes).

## Elementos administrados en el servidor

- Configuracion real del ambiente en `.env`.
- Manifiestos y dependencias de Composer.
- Directorios de arranque, recursos publicos y almacenamiento.
- Esquema y datos MySQL.
- Archivos cargados, cache, sesiones y logs.
- Tareas programadas y servicios auxiliares configurados para produccion.

Estos elementos no deben copiarse al repositorio ni ser reemplazados ciegamente durante una publicacion.

## Variables que deben definirse por ambiente

- `APP_ENV`, `APP_DEBUG`, `APP_KEY`, `APP_URL`.
- `APP_LOG` y `APP_LOG_LEVEL`.
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
- Configuracion de correo, cache, sesiones y colas usada por la instalacion real.
- Variables `V3_MONITOR_*` descritas en `OPERACION_MONITOREO.md` para los umbrales operativos.

Los valores reales deben almacenarse fuera del repositorio y no deben aparecer en capturas, tickets o documentacion compartida.

## Lista de verificacion antes de publicar codigo

1. Obtener un respaldo verificable del codigo y datos afectados en el servidor.
2. Identificar exactamente los archivos de codigo que cambiaron en Git.
3. Excluir del traslado `.env`, dependencias, almacenamiento, logs, cargas y archivos propios del servidor.
4. Confirmar si el cambio requiere una dependencia o modificacion MySQL antes de copiarlo.
5. Probar el cambio con una copia sanitizada o un ambiente de prueba cuando sea posible.
6. Verificar que `APP_DEBUG` continue desactivado en produccion.
7. Abrir `/v3/operacion-monitoreo` antes y despues de publicar.
8. Probar autenticacion, permisos y los procesos directamente afectados.
9. Confirmar logs, auditoria, PDF, XML, firma o correo cuando el cambio los involucre.
10. Mantener disponible el respaldo y un procedimiento claro de retorno.

## Criterio de aceptacion

Un despliegue se considera controlado cuando existe una lista exacta de cambios, respaldo comprobado, responsable, pruebas de humo, evidencia posterior y procedimiento de retorno, sin exponer ni sobrescribir los componentes exclusivos del servidor.
