# Contexto: Soporte e integraciones

## Estado

Mapeado. Tickets, procesos internos y sensores continúan en el legado.

## Uso

Gestiona solicitudes internas, incidentes técnicos, asignaciones, seguimiento, back office e integración futura con sensores y dispositivos.

### Módulos incluidos

- Permisos y tickets.
- Back office.
- Soporte técnico.
- Sensores.

## Límite técnico

Los conectores externos se implementarán detrás de interfaces con tiempos de espera, reintentos y registro de errores. Los módulos consumidores no conocerán detalles de transporte, credenciales ni formatos externos.

## Soporte

- Revisar estado, responsable, prioridad y trazabilidad de cada solicitud.
- Ante fallos externos, registrar el mensaje técnico sin exponer credenciales.
- Los reprocesos deben ser idempotentes para evitar duplicados.

## Criterio de migración

Empezar por tickets y consultas. Las integraciones se migrarán individualmente con adaptadores, simulaciones de prueba y monitoreo.
