# Operacion y monitoreo de v3

## Panel operativo

Ruta: `/v3/operacion-monitoreo`.

Acceso: usuario autenticado con `id_tipo_usuario = 1`.

El panel presenta:

- Conexion y latencia de MySQL.
- Disponibilidad del directorio de logs.
- Disponibilidad de `new_db_auditorias`.
- Estado de `APP_DEBUG`.
- Espacio libre en disco.
- Actividad del dia y de los ultimos siete dias.
- Usuarios activos, consultas lentas y modulos mas utilizados.
- Resumen del archivo `laravel.log` sin mostrar su contenido sensible.
- Advertencia cuando el log supera el tamano configurado.
- Diagnostico de seguridad con evidencia y recomendaciones priorizadas.
- Mantenimiento preventivo de logs, cache, almacenamiento, respaldos y scheduler.
- Seguimiento de errores pendientes y corregidos sin eliminar el registro original.

## Registro de peticiones

El middleware `operacion.v3` registra metodo, patron de ruta, estado HTTP, duracion, usuario, empresa e IP. No registra el cuerpo de la solicitud ni parametros de ruta.

Niveles:

- `info`: peticion normal.
- `warning`: respuesta 4xx o duracion mayor o igual al umbral.
- `error`: respuesta 5xx o excepcion no controlada.

Los umbrales estan centralizados en `config/monitoreo_v3.php` y pueden ajustarse mediante variables de ambiente.

## Verificacion automatizada

El comando no modifica datos y puede ser usado por una tarea externa:

```text
php artisan v3:health-check
```

Para integraciones que necesiten una respuesta estructurada:

```text
php artisan v3:health-check --json
```

El codigo de salida es `0` cuando todas las verificaciones estan correctas y `1` cuando al menos una requiere atencion. Revisa MySQL, auditoria, escritura de logs, depuracion, disco y tamano del log. El comando no fue agregado automaticamente al programador para evitar cambios operativos no autorizados.

## Umbrales

| Variable | Predeterminado | Uso |
|---|---:|---|
| `V3_MONITOR_SLOW_MS` | 2000 | Peticion web lenta. |
| `V3_MONITOR_SLOW_QUERY_MS` | 1000 | Consulta lenta registrada en auditoria. |
| `V3_MONITOR_AUDIT_DAYS` | 7 | Ventana de actividad del panel. |
| `V3_MONITOR_MIN_DISK_PERCENT` | 15 | Porcentaje libre minimo. |
| `V3_MONITOR_MAX_LOG_MB` | 100 | Tamano preventivo del log. |
| `V3_MONITOR_LOG_TAIL_BYTES` | 1048576 | Maximo leido para el resumen. |
| `V3_MONITOR_BACKUP_MARKER` | vacio | Archivo marcador actualizado solo despues de verificar un respaldo. |
| `V3_MONITOR_MAX_BACKUP_HOURS` | 24 | Antiguedad maxima aceptada para la evidencia de respaldo. |

Una falla al escribir el log no interrumpe la operacion de negocio.

## Seguimiento y mantenimiento

Marcar un error como corregido no modifica `laravel.log`. El panel guarda la huella, fecha, usuario y nota en `storage/app/v3/errores_corregidos.json`. Si aparece una ocurrencia posterior, el error vuelve automaticamente a pendientes.

El control de respaldos no crea copias. Un proceso externo autorizado debe actualizar el archivo configurado en `V3_MONITOR_BACKUP_MARKER` unicamente despues de comprobar que el respaldo existe y puede restaurarse.

El panel detecta tareas definidas en `app/Console/Kernel.php`, pero la ejecucion real de `schedule:run` debe verificarse en el cron o programador del servidor.

## Revision diaria recomendada

1. Confirmar que las cinco verificaciones de salud esten correctas.
2. Revisar errores y advertencias recientes.
3. Investigar consultas que superan el umbral configurado en auditoria.
4. Comparar el volumen del dia con el comportamiento habitual.
5. Confirmar que existe espacio suficiente y que el log se actualiza.

## Respuesta a incidentes

1. Registrar fecha, usuario afectado, empresa, modulo y accion.
2. Identificar el estado HTTP y la hora en el log.
3. Correlacionar con `new_db_auditorias` sin copiar datos sensibles.
4. Verificar MySQL, espacio, permisos de escritura y servicios relacionados.
5. Aplicar la correccion primero en un ambiente aislado.
6. Documentar causa, impacto, solucion, validacion y medida preventiva.

## Alertas pendientes

El panel es de consulta manual. Aun no existen alertas externas, retencion formal de logs, tablero historico de disponibilidad ni objetivos aprobados de servicio. Estos controles deben incorporarse antes de considerar la operacion completamente gestionada.
