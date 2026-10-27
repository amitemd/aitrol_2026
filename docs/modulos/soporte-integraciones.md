# Contexto: Soporte e integraciones

## Estado

En migración. Tickets, procesos internos y sensores continúan en el legado; la observabilidad administrativa ya utiliza contratos y adaptadores del contexto modular.

## Uso

Gestiona solicitudes internas, incidentes técnicos, asignaciones, seguimiento, back office e integración futura con sensores y dispositivos.

### Módulos incluidos

- Permisos y tickets.
- Back office.
- Soporte técnico.
- Sensores.

## Límite técnico

Los conectores externos se implementarán detrás de interfaces con tiempos de espera, reintentos y registro de errores. Los módulos consumidores no conocerán detalles de transporte, credenciales ni formatos externos.

## Observabilidad administrativa

Solo el rol administrador (`id_tipo_usuario = 1`) puede acceder a estas pantallas:

- `/administracion/observabilidad/logs`: consulta archivos `.log` legibles en `storage/logs`, filtra todos los niveles de Laravel y oculta credenciales antes de renderizar.
- `/administracion/observabilidad/auditoria`: consulta de solo lectura sobre `log_usuario` y `new_db_auditorias`, con filtros almacenados en sesión y paginación.

Los puertos `SystemLogRepository` y `AuditTrailRepository` desacoplan las pantallas de los archivos y tablas heredadas. Sus adaptadores limitan la lectura de archivos grandes, validan nombres de archivo, usan parámetros enlazados y aplican orden determinista.

Cada solicitud web atendida registra un evento `INFO` con ruta, controlador, estado, usuario, empresa, IP y duración. No se registra el contenido de formularios, contraseñas, tokens ni cookies.

## Soporte

- Revisar estado, responsable, prioridad y trazabilidad de cada solicitud.
- Ante fallos externos, registrar el mensaje técnico sin exponer credenciales.
- Los reprocesos deben ser idempotentes para evitar duplicados.
- Si el visor no muestra archivos, verificar lectura sobre `storage/logs` y escritura sobre `storage` para el usuario de PHP.
- Si una fuente de auditoría no existe, la pantalla continúa disponible y muestra una advertencia sin crear ni alterar tablas.

## Criterio de migración

Empezar por tickets y consultas. Las integraciones se migrarán individualmente con adaptadores, simulaciones de prueba y monitoreo.
