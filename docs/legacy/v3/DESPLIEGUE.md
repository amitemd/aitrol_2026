# Runbook de despliegue v3

## Objetivo

Publicar cambios del Git de desarrollo sin sobrescribir `.env`, Composer, `vendor`, `bootstrap`, `database`, `public`, `storage`, archivos cargados o configuraciones administradas en el servidor.

Este runbook no ejecuta despliegues automaticamente. Debe adaptarse y aprobarse con el responsable del servidor.

## Preflight de solo lectura

En el servidor puede verificarse la compatibilidad y presencia de componentes sin modificar nada:

```text
php artisan v3:deploy-check
php artisan v3:deploy-check --json
```

Comprueba PHP 7.1.33, Laravel 5.4, `.env`, Composer, dependencias, arranque, recursos publicos, almacenamiento escribible y `APP_DEBUG`. Devuelve codigo `0` si todo esta aprobado o `1` si requiere atencion. No instala, copia, elimina, migra ni limpia cache.

La revision humana debe registrarse en `CHECKLIST_DESPLIEGUE.md`.

## Informacion obligatoria del cambio

- Identificador y descripcion.
- Responsable tecnico y aprobador.
- Archivos modificados.
- Modulos y empresas afectadas.
- Dependencias, SQL o configuracion requerida.
- Pruebas realizadas.
- Ventana de publicacion.
- Respaldo y punto de retorno.

## Preparacion

1. Confirmar que el diff contiene solo cambios autorizados.
2. Validar sintaxis y pruebas disponibles.
3. Separar codigo de cualquier archivo exclusivo del servidor.
4. Determinar impacto en sesiones, cache, procesos programados y usuarios conectados.
5. Obtener respaldo verificable de archivos y datos afectados.
6. Definir prueba de humo y criterio de retorno.
7. Informar la ventana a los responsables.

## Publicacion

1. Registrar hora de inicio y estado previo de `/v3/operacion-monitoreo`.
2. Colocar mantenimiento solo si el impacto lo requiere y esta autorizado.
3. Transferir exclusivamente los archivos aprobados.
4. No ejecutar cambios de dependencias o MySQL si no aparecen expresamente en el plan.
5. Aplicar limpieza de cache solo cuando el cambio lo requiera.
6. Verificar permisos de lectura/escritura de los archivos tocados.

## Pruebas posteriores

1. Abrir inicio de v3 y autenticar un usuario de prueba autorizado.
2. Revisar el panel operativo y el comando `v3:health-check` cuando este disponible en el servidor.
3. Ejecutar la prueba de humo del modulo afectado.
4. Confirmar permisos, auditoria, logs y estado HTTP.
5. Verificar asiento, saldos, inventario o documentos si el cambio los involucra.
6. Registrar evidencia, hora final y responsable.

## Retorno

Retornar si existe error grave, perdida de acceso, inconsistencia contable, degradacion no prevista o falla de una dependencia critica.

1. Detener nuevas operaciones del proceso afectado de forma autorizada.
2. Restaurar los archivos desde el respaldo identificado.
3. Revertir datos solamente con un plan aprobado y evidencia; no improvisar SQL.
4. Limpiar cache si corresponde.
5. Repetir pruebas de humo y monitoreo.
6. Registrar causa del retorno y abrir analisis del incidente.

## Evidencia de cierre

El despliegue termina cuando el responsable confirma pruebas, monitoreo estable, ausencia de errores nuevos y disponibilidad del respaldo durante el periodo acordado.
