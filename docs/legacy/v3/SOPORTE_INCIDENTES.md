# Soporte e incidentes de v3

## Datos minimos del reporte

- Fecha y hora con zona horaria.
- Empresa y usuario afectados.
- Modulo, pantalla y accion.
- Identificador del documento, sin copiar informacion sensible innecesaria.
- Resultado esperado y resultado obtenido.
- Mensaje visible y estado HTTP, si existe.
- Alcance: un usuario, una empresa o todos.

## Prioridad sugerida

| Prioridad | Criterio | Respuesta inicial |
|---|---|---|
| P1 critica | Sistema indisponible, datos en riesgo o proceso contable general detenido. | Inmediata; detener operaciones afectadas y escalar. |
| P2 alta | Proceso principal bloqueado con impacto significativo y sin alternativa segura. | Mismo periodo operativo. |
| P3 media | Falla parcial con alternativa controlada. | Planificar correccion y seguimiento. |
| P4 baja | Consulta, mejora visual o problema sin impacto operativo. | Incluir en plan de trabajo. |

## Diagnostico inicial

1. Confirmar si el problema puede reproducirse sin modificar datos reales.
2. Revisar `/v3/operacion-monitoreo`.
3. Correlacionar hora, usuario, empresa y modulo con auditoria.
4. Buscar eventos `v3.peticion_atencion`, `v3.peticion_fallida` o `v3.excepcion_no_controlada`.
5. Verificar MySQL, disco, logs y `APP_DEBUG`.
6. Determinar si coincide con un despliegue reciente.
7. Preservar evidencia antes de corregir.

## Controles por tipo de problema

### Error de acceso

Revisar sesion, empresa, rol, menu y permisos. Probar tanto la opcion visible como la URL directa.

### Lentitud

Comparar duracion con `V3_MONITOR_SLOW_MS`, revisar consultas lentas, volumen del periodo, bloqueos MySQL y espacio disponible.

### Documento o asiento incorrecto

No editar directamente la base. Identificar documento origen, asiento, detalles, estado, auditoria y transaccion relacionada. Escalar al responsable contable.

### Documento electronico

Revisar estado, log de facturacion/error, XML, autorizacion, firma y conectividad. Evitar reenvios repetidos sin diagnostico.

## Cierre

Documentar causa raiz, impacto, correccion, archivos cambiados, validacion, responsable y medida preventiva. Si hubo cambio de codigo, actualizar prueba y documentacion relacionada.

## Escalamiento

Los nombres y contactos reales no se almacenan aqui. Deben mantenerse en un directorio interno autorizado y revisado por la organizacion.
