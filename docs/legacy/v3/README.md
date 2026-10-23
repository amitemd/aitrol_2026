# Documentacion del sistema v3

Actualizado: 15 de julio de 2026.

## Proposito

Este directorio documenta la version v3 del sistema contable. La version esta distribuida entre `app/v3`, `app/Http/Controllers/v3`, `app/Http/Middleware/v3`, `resources/views/v3` y `routes/v3`.

La documentacion describe solamente elementos confirmados en el codigo. Los datos que dependen del ambiente, la base MySQL o infraestructura no incluida se marcan como pendientes.

## Audiencia

- Administradores responsables de la operacion.
- Desarrolladores que mantienen Laravel y MySQL.
- Personal de soporte que atiende incidentes.
- Responsables funcionales de contabilidad.

## Mapa documental

| Documento | Contenido |
|---|---|
| `ARQUITECTURA.md` | Componentes, flujo de peticiones, modulos y dependencias. |
| `INSTALACION.md` | Prerequisitos y lista de elementos necesarios para desplegar. |
| `OPERACION_MONITOREO.md` | Panel operativo, logs, metricas y respuesta a incidentes. |
| `GUIA_DESARROLLO.md` | Reglas para modificar v3 de forma segura. |
| `MATRIZ_MADUREZ.md` | Criterios, pesos, linea base y actualizacion de la matriz. |
| `MANUAL_FUNCIONAL.md` | Guia inicial de procesos por modulo. |
| `SEGURIDAD_PERMISOS.md` | Acceso, autorizacion y proteccion de informacion. |
| `DESPLIEGUE.md` | Runbook de publicacion y retorno Git-servidor. |
| `SOPORTE_INCIDENTES.md` | Diagnostico, escalamiento y cierre de incidentes. |
| `DICCIONARIO_DATOS.md` | Familias de tablas confirmadas en los modelos. |
| `PRUEBAS.md` | Suite exclusiva de v3, alcance y forma de ejecucion. |
| `CHECKLIST_DESPLIEGUE.md` | Formato preventivo antes y despues de publicar. |

## Accesos administrativos

- Centro de documentacion: `/v3/documentacion`.
- Matriz de madurez: `/v3/matriz-madurez`.
- Operacion y monitoreo: `/v3/operacion-monitoreo`.

El centro de documentacion y el panel operativo requieren autenticacion y `id_tipo_usuario = 1`.

## Separacion entre desarrollo y servidor

Esta copia de Git se utiliza para desarrollo y excluye intencionalmente `.env`, Composer, `vendor`, `bootstrap`, `database`, `public` y `storage`, tal como consta en `.gitignore`. Los archivos operativos existen y se administran en el servidor.

Su ausencia local no se considera una falla de produccion. El riesgo pendiente es documentar y probar como se transfieren cambios de codigo sin sobrescribir el entorno, dependencias, archivos cargados, logs ni otros elementos conservados exclusivamente en el servidor.

## Regla de mantenimiento

Todo cambio que altere rutas, modulos, variables de ambiente, monitoreo o procedimientos operativos debe actualizar el documento correspondiente en la misma entrega.
