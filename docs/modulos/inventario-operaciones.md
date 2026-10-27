# Contexto: Inventario y operaciones

## Estado

Mapeado. Los movimientos continúan ejecutándose con la lógica heredada.

## Uso

Gestiona existencias, bodegas, compras, kardex, tomas físicas, mantenimiento, limpieza, cadena de frío y disponibilidad operativa.

### Módulos incluidos

- Insumos e inventario.
- Servicios generales.
- Control de temperatura.
- Limpieza y desinfección.
- Disponibilidad.

## Límite técnico

Será propietario de reservas, movimientos y saldos de inventario. Facturación solicitará reservas o consumos mediante contratos; no actualizará el kardex directamente desde sus controladores.

La misma base y las tablas actuales continuarán almacenando existencias y movimientos.

## Soporte

- Comparar documento, movimiento, kardex y saldo antes de corregir una diferencia.
- Validar empresa, bodega, producto, lote o serie según corresponda.
- Las correcciones deben ser transaccionales y conservar trazabilidad del usuario.

## Criterio de migración

Extraer primero consultas de stock. Después migrar reservas y movimientos con bloqueos, transacciones y pruebas de concurrencia.
