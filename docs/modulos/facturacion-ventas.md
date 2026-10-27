# Módulo: Facturación de ventas

## Estado

Migración incremental en curso. Las rutas y vistas continúan siendo las del sistema existente.

## Uso funcional

El módulo permite consultar, crear, editar, emitir, visualizar, anular y exportar facturas de venta. También relaciona clientes, pacientes, formas de pago, productos, seguros, asientos contables y documentos electrónicos.

### Usuarios principales

- Caja y recaudación.
- Facturación.
- Contabilidad.
- Administración autorizada.

### Flujo de consulta y edición

1. El usuario ingresa a **Cuentas y Finanzas > Ventas > Factura de Venta**.
2. Busca la factura por los filtros disponibles.
3. Abre el menú de acciones y selecciona la edición o visualización requerida.
4. El sistema valida que la factura pertenezca a la empresa activa.
5. Se muestran los datos existentes de cabecera, detalle, paciente, cliente y forma de pago.

## Diseño técnico

### Casos de uso migrados

`FindSalesInvoiceForCompany` obtiene una factura para la empresa activa. El controlador no conoce cómo se consulta la base y depende del contrato `SalesInvoiceReadRepository`.

`ListSalesInvoices` obtiene el directorio paginado de facturas para la empresa activa. Recibe filtros validados mediante `SalesInvoiceDirectoryFilters` y depende del contrato `SalesInvoiceDirectoryRepository`.

### Adaptador actual

`LaravelSalesInvoiceReadRepository` usa la conexión predeterminada de Laravel y consulta la tabla existente `ct_ventas`. No crea tablas ni utiliza una segunda base de datos.

`LaravelSalesInvoiceDirectoryRepository` conserva las columnas visibles del listado heredado, el cálculo de valor sin IVA y la comprobación de kardex. La búsqueda avanzada continúa por `POST`; la consulta global, la paginación y el límite de registros se procesan en servidor.

### Compatibilidad

`SalesInvoiceView::toLegacyRecord()` entrega temporalmente un objeto con las mismas propiedades que consumen las vistas heredadas. Esto permite migrar la lógica sin reescribir la pantalla en el mismo cambio.

El listado entrega el mismo paginador y las mismas propiedades que consume `contable/ventas/index.blade.php`. Por ello, las rutas, acciones y pantalla actual continúan funcionando mientras la consulta queda desacoplada del modelo heredado.

### Tablas relacionadas todavía gestionadas por el legado

- `ct_ventas`
- `ct_detalle_venta`
- `ct_forma_pago`
- `ct_clientes`
- `ct_asientos_cabecera`
- catálogos de bancos, divisas, seguros, sucursales y tipos de pago

## Soporte

### Factura no encontrada

Confirmar que el identificador exista en `ct_ventas` y que `id_empresa` coincida con la empresa guardada en la sesión. El módulo responde como no encontrado cuando intenta accederse a una factura de otra empresa.

### Datos incompletos en la vista

Verificar primero la fila de `ct_ventas`. Luego revisar `ct_detalle_venta` y `ct_forma_pago` usando `id_ct_ventas`. No modificar datos directamente sin un respaldo y una validación funcional.

### Error de conexión

El módulo usa la conexión `database.default`; se revisan las variables `DB_*` del entorno y los permisos del usuario de la base. No se configura una conexión propia para Facturación.

### Reversión segura

La reversión consiste en devolver la consulta del método `editar` al Query Builder previo y retirar el binding del contrato. No requiere revertir migraciones ni transformar datos.

## Verificación local

- Las pruebas validan que ambos casos de uso respeten el identificador de empresa.
- La prueba del directorio valida filtros, paginación, valor sin IVA, seguro y existencia de kardex.
- Las pruebas del contenedor validan los contratos y sus adaptadores.
- Las pruebas de compatibilidad existentes continúan validando rutas y vista de ventas.
- La revisión manual debe abrir una factura real de la empresa activa y otra inexistente o perteneciente a otra empresa.
