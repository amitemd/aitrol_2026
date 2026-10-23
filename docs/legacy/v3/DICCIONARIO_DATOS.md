# Diccionario inicial de datos v3

## Alcance y limitacion

Este inventario se basa en las propiedades `$table` de los modelos v3. Confirma nombres y familias funcionales, pero no sustituye el esquema MySQL del servidor. Columnas, tipos, claves, indices, restricciones y relaciones deben completarse desde una exportacion estructural autorizada.

## Convenciones observadas

- `ct_`: procesos contables y administrativos.
- `af_`: activos fijos.
- `inv_`: inventario alterno o transaccional.
- `de_`: documentos electronicos.
- `new_db_`: configuracion, seguridad y utilidades de v3.
- `log_`: trazabilidad o procesos historicos.

## Contabilidad

| Tabla | Proposito inferido del modelo |
|---|---|
| `ct_asientos_cabecera` | Cabecera del asiento contable. |
| `ct_asientos_detalle` | Lineas de debe y haber. |
| `plan_cuentas_empresa` | Plan de cuentas utilizado por empresa. |
| `ct_comprobante_secuencia` | Secuencias de comprobantes. |
| `ct_configuraciones` | Parametros contables. |

## Ventas y clientes

| Tabla | Proposito inferido del modelo |
|---|---|
| `ct_ventas` | Cabecera de ventas. |
| `ct_detalle_venta` | Detalle de productos/servicios vendidos. |
| `ct_clientes` | Maestro contable de clientes. |
| `ct_comprobante_ingreso` | Cobros o ingresos de clientes. |
| `ct_detalle_comprobante_ingreso` | Aplicacion y forma de pago del ingreso. |
| `ct_cruce_cuentas_clientes` | Cruces de cuentas de clientes. |
| `ct_cruce_valores_cliente` | Cruces de valores de clientes. |
| `ct_nota_credito_clientes` | Notas de credito de clientes. |
| `ct_cliente_retencion` | Retenciones recibidas de clientes. |

## Compras y proveedores

| Tabla | Proposito inferido del modelo |
|---|---|
| `ct_compras` | Cabecera de compras. |
| `ct_detalle_compra` | Detalle de compras. |
| `proveedor` | Maestro general de proveedores. |
| `ct_comprobante_egreso` | Pagos o egresos. |
| `ct_detalle_comprobante_egreso` | Aplicacion del egreso. |
| `ct_credito_acreedores` | Creditos/notas de proveedores. |
| `ct_debito_acreedores` | Debitos de proveedores. |
| `ct_retenciones` | Retenciones a proveedores. |

## Caja y bancos

| Tabla | Proposito inferido del modelo |
|---|---|
| `ct_caja_banco` | Cuentas de caja y bancos. |
| `ct_bancos` | Catalogo/configuracion bancaria. |
| `ct_transferencia_bancaria` | Transferencias. |
| `ct_deposito_bancario` | Depositos. |
| `ct_debito_bancario` | Debitos o notas bancarias. |
| `ct_conciliacion_mes` | Estado de conciliacion por periodo. |
| `ct_conciliacion_bancaria` | Movimientos conciliados. |
| `ct_conciliacion_pendientes` | Documentos pendientes de conciliacion. |

## Inventario

| Tabla | Proposito inferido del modelo |
|---|---|
| `ct_productos` | Productos contables. |
| `ct_bodegas` | Bodegas. |
| `ct_kardex` | Kardex contable. |
| `ct_inventario` | Inventario contable. |
| `inv_cab_movimientos` | Cabecera de movimientos de inventario. |
| `inv_det_movimientos` | Detalle de movimientos. |
| `inv_inventario` | Existencias del modulo `inv`. |
| `inv_kardex` | Kardex del modulo `inv`. |

## Activos fijos

| Tabla | Proposito inferido del modelo |
|---|---|
| `af_activo` | Maestro de activos. |
| `af_grupo` | Grupos de activos. |
| `af_tipo` | Tipos y cuentas relacionadas. |
| `af_subtipo` | Subtipos. |
| `af_depreciacion_cabecera` | Proceso de depreciacion por periodo. |
| `af_depreciacion_detalle` | Depreciacion por activo. |
| `af_activo_accesorios` | Accesorios asociados. |

## Tributario y documentos electronicos

| Tabla | Proposito inferido del modelo |
|---|---|
| `ct_ats` / `ct_detalle_ats` | Cabecera y detalle ATS. |
| `de_documentos_electronicos` | Estado/documento electronico. |
| `de_log_facturacion` | Trazabilidad de facturacion electronica. |
| `de_log_error` | Errores de documentos electronicos. |
| `de_info_tributaria` | Informacion tributaria relacionada. |
| `de_maestro_documentos` | Catalogo de tipos/documentos. |

## Configuracion, usuarios y auditoria

| Tabla | Proposito inferido del modelo |
|---|---|
| `users` | Usuarios autenticables. |
| `empresa` | Empresas. |
| `new_db_menu` / `new_db_submenu` | Navegacion y opciones. |
| `new_db_permisos_usuario` | Permisos por usuario/empresa. |
| `new_db_roles_usuarios` | Roles de v3. |
| `new_db_catalogo` | Catalogos generales. |
| `new_db_config_sistema` | Configuracion del sistema. |
| `new_db_auditorias` | Auditoria de acciones y consultas. |
| `new_db_archivos` | Metadatos de archivos. |

## Trabajo pendiente controlado

Para completar el diccionario se necesita una exportacion solo de estructura del MySQL autorizado. Por cada tabla deben documentarse propietario funcional, columnas, tipo, nulabilidad, valor predeterminado, clave primaria, claves foraneas, indices, volumen, retencion y clasificacion de sensibilidad.
