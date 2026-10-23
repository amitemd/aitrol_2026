# Manual funcional de opciones confirmadas en v3

## Alcance y criterio

Este manual documenta las pantallas que tienen una entrada real en los controladores de `v3` al 15/07/2026.
No utiliza enlaces generales como `/v3/configuraciones`, porque esa direccion no identifica una funcion concreta.

Cada ruta incluida cumple estas condiciones:

- El controlador reconoce el nombre del submodulo.
- La opcion prepara una vista Blade de usuario.
- La ruta puede representar una pantalla de consulta, mantenimiento o registro.
- No es solamente una operacion AJAX, `save`, busqueda, cambio de estado, impresion, PDF, XML o descarga.

La visibilidad final depende de la empresa, el usuario y los permisos configurados. Una opcion confirmada en codigo
puede no aparecer en el menu de todos los usuarios.

## Reglas comunes de operacion

Antes de registrar o modificar informacion:

1. Confirmar la empresa y el periodo activos.
2. Verificar que el usuario tenga permiso para la opcion.
3. Revisar fecha, tercero, sucursal, punto de emision y secuencia cuando corresponda.
4. Comprobar valores, impuestos, forma de pago, cuenta contable y documentos relacionados.
5. No continuar si el periodo esta cerrado o la informacion obligatoria esta incompleta.
6. Verificar el resultado contable, tributario y de inventario despues de guardar.

Las anulaciones deben ejecutarse desde la opcion prevista por el sistema. No se deben eliminar movimientos
directamente en MySQL.

## Configuracion del sistema

| Opcion | Ingreso real | Funcion |
|---|---|---|
| Ubicaciones | `/v3/configuracionessistema/ubicaciones` | Mantener paises, provincias y ciudades. |
| Especialidades | `/v3/configuracionessistema/especialidades` | Mantener especialidades. |
| Procedimientos | `/v3/configuracionessistema/procedimientos` | Mantener procedimientos utilizados por v3. |

## Configuraciones administrativas y contables

| Opcion | Ingreso real | Funcion |
|---|---|---|
| Menu | `/v3/configuraciones/menu` | Configurar modulos visibles. |
| Submenus | `/v3/configuraciones/subMenu` | Configurar opciones de cada modulo. |
| Permisos | `/v3/configuraciones/permisos` | Asignar accesos por usuario. |
| Usuarios | `/v3/configuraciones/usuarios` | Consultar y mantener usuarios. |
| Perfil | `/v3/configuraciones/perfil` | Consultar el perfil del usuario activo. |
| Empresas | `/v3/configuraciones/empresas` | Mantener empresas y representantes. |
| Roles | `/v3/configuraciones/roles` | Mantener roles de usuario. |
| Establecimientos | `/v3/configuraciones/establecimientos` | Configurar establecimientos y datos relacionados. |
| Catalogos | `/v3/configuraciones/catalogo` | Mantener catalogos generales. |
| Subcatalogos | `/v3/configuraciones/subCatalogo` | Mantener valores dependientes de catalogos. |
| Auditoria | `/v3/configuraciones/auditoria` | Consultar actividad por empresa y usuario. |
| Bodegas | `/v3/configuraciones/bodegas` | Mantener bodegas contables. |
| Planes | `/v3/configuraciones/planes` | Mantener planes disponibles. |
| Seleccion de plan | `/v3/configuraciones/selecionarplan` | Asignar planes. El nombre de ruta conserva la ortografia implementada. |

Controles funcionales:

- Solo un administrador autorizado debe cambiar menus, permisos, usuarios, empresas, roles o establecimientos.
- Antes de desactivar un registro, revisar si tiene movimientos relacionados.
- Los cambios de firma, punto de emision o secuencial deben probarse antes de emitir documentos.

## Utilidades del usuario

| Opcion | Ingreso real | Funcion |
|---|---|---|
| Calendario | `/v3/utilidades/calendario` | Consultar eventos y actividades. |
| Cambiar clave | `/v3/utilidades/cambiarclave` | Actualizar la clave del usuario. |

Al cambiar una clave, confirmar la identidad del usuario y evitar claves reutilizadas o compartidas.

## Contabilidad

| Opcion | Ingreso real | Funcion |
|---|---|---|
| Plan de cuentas | `/v3/contabilidad/plandecuentas` | Consultar y mantener cuentas contables. |
| Asientos descuadrados | `/v3/contabilidad/descuadrados` | Identificar asientos cuyo debe y haber no coinciden. |
| Asientos anulados | `/v3/contabilidad/anulados` | Consultar asientos anulados. |

Revision minima:

1. Revisar primero los asientos descuadrados.
2. Identificar el documento origen y no corregir solamente el asiento final.
3. Confirmar que debe y haber coincidan y que el periodo sea correcto.
4. Documentar la correccion y volver a validar los reportes afectados.

## Ventas

| Opcion | Ingreso real | Funcion |
|---|---|---|
| Lista de ventas | `/v3/ventas/listadeventas` | Buscar, consultar, imprimir y gestionar ventas. |
| Nueva venta | `/v3/ventas/facturar` | Registrar una factura de venta. |
| Factura de caja | `/v3/ventas/facturadecaja` | Registrar facturacion desde caja. |
| Venta desde orden | `/v3/ventas/ventaOrden` | Facturar una orden existente. |
| Informacion tributaria | `/v3/ventas/crearInfoTributaria` | Preparar datos tributarios necesarios para facturar. |

Flujo recomendado para una venta:

1. Ingresar por Nueva venta, Factura de caja o Venta desde orden segun el origen.
2. Seleccionar cliente, establecimiento y punto de emision.
3. Agregar productos o servicios y comprobar disponibilidad.
4. Revisar precios, descuentos, impuestos, total y formas de pago.
5. Guardar una sola vez y comprobar el numero generado.
6. Verificar la venta desde Lista de ventas.
7. Confirmar asiento, inventario, cartera y estado electronico cuando aplique.

## Compras

| Opcion | Ingreso real | Funcion |
|---|---|---|
| Lista de compras | `/v3/compras/listadecompras` | Buscar y gestionar compras. |
| Nueva compra | `/v3/compras/addcompra` | Registrar una factura de compra. |
| Nuevo gasto | `/v3/compras/addgasto` | Registrar un gasto sin movimiento de inventario. |
| Preorden | `/v3/compras/preOrden` | Preparar una orden de compra. |
| Compra desde orden | `/v3/compras/nuevaFacturaOrden` | Convertir una orden en compra. |
| Compra de activos | `/v3/compras/nuevaFacturaActivos` | Registrar una factura asociada a activos fijos. |
| Nuevo activo desde compras | `/v3/compras/addActivos` | Crear activos relacionados con una compra. |

Flujo recomendado para una compra:

1. Seleccionar el tipo correcto: compra, gasto, orden o activo.
2. Confirmar proveedor, documento, autorizacion, fecha y establecimiento.
3. Revisar bodega, cuentas, impuestos, retenciones y total.
4. Guardar y verificar la compra en Lista de compras.
5. Confirmar asiento, inventario, deuda del proveedor y activos cuando correspondan.

## Clientes, cobros y cartera

| Opcion | Ingreso real | Funcion |
|---|---|---|
| Lista de clientes | `/v3/clientes/listadeclientes` | Consultar y mantener clientes. |
| Comprobantes de ingreso | `/v3/clientes/comprobantedeingreso` | Consultar cobros aplicados a documentos. |
| Nuevo comprobante de ingreso | `/v3/clientes/nuevo_compIngreso` | Registrar un cobro de cliente. |
| Ingresos varios | `/v3/clientes/comprobantedeingresovarios` | Consultar ingresos sin factura relacionada. |
| Nuevo ingreso varios | `/v3/clientes/nuevo_compIngresoVarios` | Registrar un ingreso varios. |
| Cruce de cuentas | `/v3/clientes/crucedecuentas` | Consultar cruces contables del cliente. |
| Nuevo cruce de cuentas | `/v3/clientes/nuevo_cruce_cuentas` | Registrar un cruce de cuentas. |
| Cruce de valores | `/v3/clientes/crucedevalores` | Consultar anticipos y valores aplicados. |
| Nuevo cruce de valores | `/v3/clientes/nuevo_cruce_valores` | Aplicar valores disponibles a deudas. |
| Notas de credito | `/v3/clientes/notadecredito` | Consultar notas de credito emitidas. |
| Nueva nota de credito | `/v3/clientes/nuevanotadecredito` | Emitir una nota de credito a un cliente. |
| Retenciones de clientes | `/v3/clientes/retenciones` | Consultar retenciones recibidas. |
| Nueva retencion de cliente | `/v3/clientes/nuevaretencion` | Registrar una retencion recibida. |
| Rubros de clientes | `/v3/clientes/rubros` | Mantener conceptos aplicables a clientes. |

Antes de cobrar, cruzar o emitir una nota, confirmar documento origen, saldo pendiente, valor, fecha y forma de pago.
El saldo resultante debe coincidir con cartera y contabilidad.

## Proveedores, pagos y obligaciones

| Opcion | Ingreso real | Funcion |
|---|---|---|
| Lista de proveedores | `/v3/proveedores/listadeproveedores` | Consultar y mantener proveedores. |
| Nuevo proveedor | `/v3/proveedores/proveedor` | Registrar o editar un proveedor. |
| Retenciones | `/v3/proveedores/retenciones` | Consultar retenciones emitidas. |
| Nueva retencion | `/v3/proveedores/nuevaretencion` | Emitir una retencion. |
| Saldos iniciales | `/v3/proveedores/saldosiniciales` | Consultar obligaciones iniciales. |
| Nuevo saldo inicial | `/v3/proveedores/nuevo_saldoinicial` | Registrar una obligacion inicial. |
| Cruce de cuentas | `/v3/proveedores/crucedecuentas` | Consultar cruces de cuentas. |
| Nuevo cruce de cuentas | `/v3/proveedores/nuevo_cruce_cuentas` | Registrar un cruce de cuentas. |
| Rubros | `/v3/proveedores/rubros` | Mantener conceptos de proveedor. |
| Comprobantes de egreso | `/v3/proveedores/comprobantedeegreso` | Consultar pagos a proveedores. |
| Comprobantes de egreso varios | `/v3/proveedores/comprobantedeegresovario` | Consultar pagos a otros acreedores. |
| Notas de credito | `/v3/proveedores/notadecredito` | Consultar notas de credito recibidas. |
| Nueva nota de credito | `/v3/proveedores/nuevanotadecredito` | Registrar una nota de credito recibida. |
| Notas de debito | `/v3/proveedores/notadedebito` | Consultar notas de debito recibidas. |
| Nueva nota de debito | `/v3/proveedores/nuevanotadedebito` | Registrar una nota de debito recibida. |
| Cruce de valores | `/v3/proveedores/crucedevalores` | Consultar anticipos aplicados. |
| Nuevo cruce de valores | `/v3/proveedores/crearCruceValor` | Aplicar anticipos o valores a obligaciones. |

Antes de pagar o cruzar valores, revisar proveedor, deuda, cuenta bancaria, soporte, retenciones y saldo final.

## Caja, bancos y conciliacion

| Opcion | Ingreso real | Funcion |
|---|---|---|
| Lista de bancos y cajas | `/v3/cajabancos/listadebancos` | Consultar y mantener cuentas financieras. |
| Nueva caja o banco | `/v3/cajabancos/crearcajabanco` | Crear una cuenta de caja o banco. |
| Transferencias | `/v3/cajabancos/transferenciabancaria` | Consultar transferencias. |
| Nueva transferencia | `/v3/cajabancos/crearTransferenciaBancaria` | Registrar una transferencia. |
| Depositos | `/v3/cajabancos/depositobancario` | Consultar depositos. |
| Nuevo deposito | `/v3/cajabancos/crearDepositoBancario` | Registrar un deposito. |
| Notas de credito bancarias | `/v3/cajabancos/notadecredito` | Consultar notas de credito bancarias. |
| Nueva nota de credito bancaria | `/v3/cajabancos/crearNotaCredito` | Registrar una nota de credito bancaria. |
| Notas de debito bancarias | `/v3/cajabancos/notadedebito` | Consultar notas de debito bancarias. |
| Nueva nota de debito bancaria | `/v3/cajabancos/notadedebitobancaria` | Registrar una nota de debito bancaria. |
| Nueva nota de debito de caja/banco | `/v3/cajabancos/nuevanotadedebitocajabanco` | Registrar una nota de debito asociada a caja o banco. |
| Anticipos | `/v3/cajabancos/registraranticipo` | Registrar anticipos. |
| Caja chica | `/v3/cajabancos/cajachica` | Gestionar fondos de caja chica. |
| Movimientos bancarios | `/v3/cajabancos/movimientosbancarios` | Consultar movimientos por cuenta. |
| Conciliacion bancaria | `/v3/cajabancos/conciliacion` | Consultar conciliaciones. |
| Nueva conciliacion | `/v3/cajabancos/agregarConciliacion` | Registrar una conciliacion. |

Para conciliar, confirmar cuenta, periodo, saldo inicial y movimientos del estado bancario. Las diferencias deben
explicarse antes de guardar o cerrar la conciliacion.

## Inventario

| Opcion | Ingreso real | Funcion |
|---|---|---|
| Lista de productos | `/v3/inventario/listadeproductos` | Consultar y mantener productos y relaciones. |

Esta es la unica pantalla directa confirmada actualmente en el controlador de Inventario. Existen modelos para
kardex, bodegas y movimientos, pero no se documentan como opciones de ingreso hasta confirmar una pantalla real.

## Activos fijos

| Opcion | Ingreso real | Funcion |
|---|---|---|
| Grupos | `/v3/activosfijos/grupos` | Mantener grupos y tipos de activos. |
| Subtipos | `/v3/activosfijos/subtipos` | Mantener subtipos y codificacion. |
| Lista de activos | `/v3/activosfijos/listadeactivos` | Consultar y mantener activos. |
| Depreciaciones | `/v3/activosfijos/depreciaciones` | Consultar depreciaciones procesadas. |
| Nueva depreciacion | `/v3/activosfijos/crearDepreciacion` | Preparar y registrar una depreciacion. |
| Historial de depreciacion | `/v3/activosfijos/historialDepreciacion` | Consultar historial por activo. |

Antes de depreciar, verificar fecha de compra, costo, vida util, cuentas, depreciacion acumulada y periodo.

## Guias de remision

| Opcion | Ingreso real | Funcion |
|---|---|---|
| Transportistas | `/v3/guiaderemision/transportista` | Consultar y mantener transportistas. |
| Lista de guias | `/v3/guiaderemision/listadeguias` | Buscar y gestionar guias. |
| Nueva guia | `/v3/guiaderemision/crearGuia` | Registrar una guia de remision. |

Flujo recomendado:

1. Verificar o registrar el transportista.
2. Crear la guia con destinatario, fechas, placa, motivo y detalle.
3. Validar establecimiento, punto de emision y documento sustento.
4. Guardar y comprobar la guia desde Lista de guias.
5. Si es electronica, verificar XML, firma, envio y autorizacion.

## Tributario y documentos electronicos

| Opcion | Ingreso real | Funcion |
|---|---|---|
| Gestion de documentos | `/v3/tributario/gestiondocumentos` | Consultar estados, validaciones, logs y reprocesos. |
| Declaraciones | `/v3/tributario/declaraciones` | Consultar informacion para declaraciones. |
| ATS | `/v3/tributario/ats` | Generar y consultar el anexo transaccional. |
| Importar documentos | `/v3/tributario/importardocumentos` | Cargar documentos tributarios externos. |

Ante un documento detenido:

1. Ingresar a Gestion de documentos y filtrar fecha, tipo y estado.
2. Revisar detalle, clave de acceso, estado y logs.
3. Consultar al SRI cuando exista una clave valida.
4. Corregir datos, configuracion tributaria, secuencial, firma o conectividad segun el error.
5. Validar XML/XSD antes del reproceso.
6. Reprocesar solo si no esta autorizado y la validacion es correcta.

## Importaciones

| Opcion | Ingreso real | Funcion |
|---|---|---|
| Lista de importaciones | `/v3/importaciones/listadeimportaciones` | Consultar procesos de importacion. |
| Nueva factura de importacion | `/v3/importaciones/ingresa_factura` | Registrar compras y gastos de importacion. |

Verificar proveedor, archivo/documento origen, bodegas, terminos, comprobantes y gastos. Reimportar el mismo origen
no debe duplicar compras ni asientos.

## Recursos humanos

| Opcion | Ingreso real | Funcion |
|---|---|---|
| Configuraciones de nomina | `/v3/recursoshumanos/configuraciones` | Mantener parametros de nomina. |
| Lista de empleados | `/v3/recursoshumanos/listadeempleados` | Consultar empleados y datos laborales. |
| Lista de roles | `/v3/recursoshumanos/listaderoles` | Consultar roles de pago. |

El alcance funcional de nomina debe validarse con un ciclo completo antes de considerarlo certificado.

## Reportes

| Opcion | Ingreso real | Funcion |
|---|---|---|
| Proveedores | `/v3/reportes/proveedores` | Cartera por pagar, saldos, deudas y pagos. |
| Ventas | `/v3/reportes/ventas` | Informes de ventas. |
| Contables | `/v3/reportes/contables` | Diario, mayor, balances y resultados. |
| Clientes | `/v3/reportes/clientes` | Cartera, saldos y deudas. |
| Financieros | `/v3/reportes/financieros` | Balance financiero y resultados. |
| Activos fijos | `/v3/reportes/activosfijos` | Informes de activos y depreciacion. |

Los reportes deben validarse contra documentos origen, auxiliares y mayor contable usando el mismo periodo y empresa.

## Gobierno y soporte

| Opcion | Ingreso real | Funcion |
|---|---|---|
| Inicio v3 | `/v3/admin` | Mostrar el tablero inicial y los accesos disponibles. |
| Documentacion | `/v3/documentacion` | Consultar manuales tecnicos y funcionales. |
| Matriz de madurez | `/v3/matriz-madurez` | Consultar estado, brechas y prioridades por modulo. |
| Operacion y monitoreo | `/v3/operacion-monitoreo` | Consultar salud, actividad y errores observados. |

## Acciones que no son opciones de ingreso

No se presentan como menu funcional las rutas que guardan, buscan, imprimen, descargan o cambian estados, aunque
formen parte de un proceso. Ejemplos: `save...`, `get...`, `buscar...`, `...js`, `descargarXML`, `descargarPDF`,
`estado...`, `anular...` y endpoints API.

Estas acciones deben ejecutarse desde la pantalla que las invoca, porque abrirlas directamente puede requerir un ID,
datos POST o una seleccion previa.

## Validacion pendiente

Para convertir este manual en un procedimiento operativo aprobado todavia se requiere, por cada opcion:

- Responsable y rol autorizado.
- Campos obligatorios y ejemplo valido.
- Resultado esperado en pantalla y base de datos.
- Resultado contable, tributario o de inventario.
- Caso de anulacion o recuperacion.
- Evidencia de prueba con datos controlados.
