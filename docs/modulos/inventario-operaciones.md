# Contexto: Inventario y operaciones

## Estado

En migración. La consulta de existencias ya atraviesa un caso de uso y un adaptador modular; los movimientos continúan ejecutándose con la lógica heredada.

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

## Operación migrada: consulta de existencias

La pantalla heredada conserva su ruta y sus filtros, pero delega la lectura a `SearchInventoryStock` mediante `InventoryStockReadRepository`. El adaptador consulta las tablas actuales `inv_inventario`, `producto`, `marca` y `bodega` usando la conexión predeterminada.

La consulta aplica siempre el identificador de la empresa activa y admite filtros por producto, bodega, tipo y nombre. Productos, marcas y bodegas se resuelven en una sola consulta; la vista ya no provoca consultas adicionales por cada fila.

### Riesgo y reversión

La operación es exclusivamente de lectura y no modifica existencias. Para revertirla basta restaurar la consulta del controlador y los accesos de la vista; no existe migración de datos asociada.

## Soporte

- Comparar documento, movimiento, kardex y saldo antes de corregir una diferencia.
- Validar empresa, bodega, producto, lote o serie según corresponda.
- Si una fila no muestra producto, marca o bodega, verificar las relaciones por identificador en las cuatro tablas antes de alterar datos.
- Las correcciones deben ser transaccionales y conservar trazabilidad del usuario.

## Criterio de migración

Extraer primero consultas de stock. Después migrar reservas y movimientos con bloqueos, transacciones y pruebas de concurrencia.
