# Contexto: Contabilidad y finanzas

## Estado

En migración. La lectura de líneas para editar asientos ya atraviesa un caso de uso y un adaptador modular; los demás procesos financieros continúan en el legado.

## Uso

Administra plan de cuentas, diarios, asientos, cartera, bancos, balances, activos, depreciaciones, amortizaciones y planeación financiera.

### Módulos incluidos

- Contabilidad.
- Activos fijos.
- Planeación financiera.
- Amortizaciones.

## Límite técnico

Será propietario del libro contable. Facturación y otros contextos solicitarán la contabilización mediante contratos o eventos; no insertarán detalles contables directamente una vez migrada la operación correspondiente.

Planeación financiera consumirá proyecciones y saldos mediante modelos de lectura, sin depender de controladores contables.

## Operación migrada: líneas de un asiento

La edición conserva la ruta, autorización, empresa activa y vista heredadas, pero obtiene sus líneas mediante `ListJournalEntryLinesForEditing` y `JournalEntryLineReadRepository`. El adaptador consulta las tablas actuales `ct_asientos_detalle` y `plan_cuentas_empresa`.

Las líneas se resumen por cuenta con `SUM` para debe y haber. Todos los campos no agregados forman parte del `GROUP BY`, por lo que la operación funciona con `ONLY_FULL_GROUP_BY` sin debilitar la configuración de MySQL. El nombre de la cuenta se entrega en el mismo resultado y evita consultas adicionales desde la vista.

### Riesgo y reversión

Esta etapa es exclusivamente de lectura. No crea tablas, no altera asientos y no cambia el flujo de actualización heredado. Puede revertirse restaurando la consulta del controlador y los campos anteriores de la vista.

## Soporte

- Verificar cabecera, detalles, empresa, período y balance entre debe y haber.
- No desactivar `ONLY_FULL_GROUP_BY`; corregir las consultas incompatibles.
- Si los totales no coinciden, comparar las líneas originales por `id_asiento_cabecera`, empresa e `id_plan_cuenta` antes de editar.
- Antes de cambiar una cuenta o asiento, revisar cierres y documentos de origen.

## Criterio de migración

Comenzar por consultas y reportes, continuar con generación de asientos por contrato y dejar cierres o reprocesos para la última etapa.
