# Aitrol 2026

Aitrol es un ERP administrativo multiempresa. Centraliza la operación comercial, financiera, contable, tributaria y de recursos humanos. Cada sesión trabaja con su empresa activa. Las pantallas operativas que aplican contexto usan también la sucursal y el período seleccionados.

<!-- vicky:resumen -->
Aitrol 2026 es un ERP administrativo multiempresa organizado en 10 módulos: Activos fijos, Caja y bancos, Clientes, Comercial, Configuración, Contabilidad, Inventarios, Proveedores, Recursos humanos y Tributario. Comercial reúne Ventas y Compras en un mismo paquete.

La arquitectura es un monolito modular en Laravel 13 y PHP 8.4. Los paquetes organizan los casos de uso en Application y las rutas, controladores y persistencia en Infrastructure. Algunos módulos también tienen una capa Domain. La interfaz usa Blade, AdminLTE y JavaScript. El espacio de trabajo abre las pantallas en pestañas internas y las altas compatibles en ventanas modales.
<!-- /vicky:resumen -->

## Módulos registrados

| Módulo | Paquete | Responsabilidad |
| --- | --- | --- |
| Activos fijos | `activos-fijos` | Registro de activos, ubicaciones, movimientos, bajas, depreciaciones y mantenimiento. |
| Caja y bancos | `caja-bancos` | Cuentas, ingresos y egresos, transferencias, depósitos, cierres, extractos y conciliación. |
| Clientes | `clientes` | Directorio y fichas de clientes, con secciones para sus documentos y reportes. |
| Comercial | `comercial` | Ventas, compras, cotizaciones, órdenes de compra, ajustes, retenciones y reportes. |
| Configuración | `configuracion` | Empresas, sucursales, ubicaciones, usuarios, roles, permisos, menú y contexto operativo. |
| Contabilidad | `contabilidad` | Plan de cuentas, asientos, períodos, integración con otros módulos y estados financieros. |
| Inventarios | `inventarios` | Productos y servicios, bodegas, categorías, existencias, movimientos y kárdex. |
| Proveedores | `proveedores` | Directorio y fichas de proveedores, con secciones para sus documentos y reportes. |
| Recursos humanos | `rrhh` | Empleados, organización, contratos, asistencia, permisos, vacaciones, nómina, décimos y otros procesos de personal. |
| Tributario | `tributario` | Preparación de formularios, ATS, registro de declaraciones y documentos de respaldo. |

El inventario corresponde a los paquetes de `packages/modules/` registrados en `bootstrap/providers.php`. Algunas secciones del menú todavía dependen de una fuente de datos. Vicky puede dirigir a los reportes de cartera si el usuario tiene permiso. Para responder importes de cartera desde el chat todavía se requieren las vistas de lectura correspondientes. Pacientes exige una fuente operativa habilitada por empresa.

### Flujos de cartera y firma de pruebas

- Ventas: una factura contabilizada admite cobros totales o parciales desde su cartera. La API de ventas también registra cobros y acepta la misma clave de operación al reintentar.
- Compras y Proveedores: las facturas contabilizadas alimentan cuentas por pagar, saldos por proveedor, vencidos y antigüedad al corte. Las notas y retenciones contabilizadas ajustan el saldo. El plazo de cartera puede indicarse en días al registrar la compra. Sin ese dato se usa la fecha de la factura.
- Caja y bancos: registra el pago a proveedor desde Cuentas por pagar. El egreso debita la cuenta contable de proveedores y queda vinculado a la factura. La reversión en Caja y bancos devuelve el importe a la cartera. El historial identifica el comprobante revertido.
- Facturas de pruebas: en Ventas → Lista de ventas, abre **Autorización SRI** para firmar una factura de pruebas con el certificado PKCS#12 de la empresa activa. Con `SRI_ENVIO_FACTURAS_PRUEBAS=true`, el usuario autorizado puede enviar el XML firmado al SRI de pruebas. El historial registra la recepción, los mensajes de rechazo y el resultado de autorización. En un reintento se consulta primero la clave. La API ofrece la misma acción en `POST /api/v1/ventas/documentos/{registro}/firma-local/enviar`. Para validar con el SRI, usa un comprobante y certificado del ambiente de certificación. [Ficha técnica oficial](https://www.sri.gob.ec/web/intersri/facturacion-electronica)
- API v1: consulta los reportes bajo `/api/v1/proveedores/reportes/` y los comprobantes en `/api/v1/proveedores/documentos/comprobantes-de-pago`. Registra pagos en `POST /api/v1/proveedores/pagos/{factura}` y cobros en `POST /api/v1/ventas/documentos/{registro}/cobro`. Ventas ofrece consulta, firma y descarga en `/api/v1/ventas/documentos/{registro}/firma-local` y `/api/v1/ventas/documentos/{registro}/firma-local/xml`. Consulta OpenAPI para los alcances, permisos y contexto de sucursal.
- Credenciales de integraciones: añade `--pagos` a `proveedores:token` para autorizar pagos, o `--cobros` a `comercial:token ventas` para autorizar cobros. Ambos flujos comprueban también los permisos del usuario en Caja y bancos.

### Recepción parcial de compras

1. En Compras → Órdenes de compra, elige la orden y abre **Recepciones de la orden**. Elige una bodega activa de la sucursal y registra solo las cantidades de productos físicos entregadas en el día. La orden muestra las cantidades recibidas y las que faltan por entregar.
2. Cuando llegue el comprobante, selecciona las recepciones que incluye e ingresa sus cantidades, precios, subtotal e impuesto. Una factura puede consumir parte de una recepción y dejar saldo para otra. Si seleccionas varias, las cantidades se aplican primero a la entrega más antigua. Puedes agregar servicios incluidos en la misma orden. La fecha del comprobante debe ser igual o posterior a las entregas seleccionadas.
3. Si cambió el precio, la base o el IVA respecto de la orden y lo recibido, el usuario con permiso de escritura en **Asientos contables** registra un motivo al guardar. La trazabilidad de la factura muestra quién aprobó, qué cambió y cuánto aplicó a cada recepción. Las cantidades de mercadería están limitadas a lo recibido y las de servicios al saldo de la orden.
4. Ve a Proveedores → Reportes → Cuentas por pagar para registrar un pago total o parcial. Si se anula una factura, queda disponible la cantidad que había consumido. Para revertir toda una recepción, primero deben anularse sus facturas vigentes y, si corresponde, revertirse los pagos.

Para habilitar el flujo, aplica las migraciones y vincula **Mercadería recibida por facturar** en la integración contable de la empresa. Usa una cuenta de pasivo distinta de **Proveedores**, con Inventario e IVA en compras asignados y el período abierto. Los ingresos a bodega se contabilizan contra esa cuenta transitoria. El cotejo salda esa cuenta y pasa el total a cuentas por pagar. El registro es interno y no envía comprobantes al SRI.

Para facturas mixtas, asigna **Compras** para la base de los servicios. Si la base facturada de la mercadería difiere de la base recibida, asigna **Diferencias de precio en compras**. El asiento liquida únicamente la base de la recepción aplicada y registra la diferencia en su cuenta contable. El inventario conserva el costo registrado al recibir.

La API v1 usa `/api/v1/compras/ordenes-compra/{orden}/recepciones` para consultar y registrar entregas, y los sufijos `/facturar` y `/{recepcion}/anular` para las operaciones restantes. Envía `modo_cotejo: parcial` para dividir una recepción o incluir servicios y ajustes. El modo `completo` sigue siendo el valor predeterminado. El detalle en `GET /api/v1/compras/documentos/{registro}` incluye las aplicaciones y la aprobación, cuando corresponde. Las escrituras requieren `compras:escribir` además de `compras:leer` y los permisos de cada pantalla. Al reintentar una recepción, conserva el mismo UUID en `clave_operacion` y envía los mismos datos.

### Devoluciones y compras pendientes

- En **Compras → Lista de compras → Ver trazabilidad → Devoluciones de mercadería**, selecciona las cantidades por recepción e ingresa el número de nota de crédito del proveedor. El sistema registra la salida de la bodega original, prorratea la base e IVA de lo facturado y aplica el crédito a la cartera. La anulación de la nota repone el stock y revierte el asiento. Para registrar se requieren permisos de escritura en **Notas de crédito** y **Movimientos de inventario**. Una devolución corrige la factura y conserva el historial de lo recibido en la orden.
- En **Compras → Reportes → Compras pendientes**, busca por orden, proveedor o producto de la sucursal activa. Los filtros muestran mercadería pendiente de recibir o recibida pendiente de facturar. Los importes se muestran como base sin IVA. Los saldos incluyen órdenes vigentes de períodos anteriores.
- La API consulta y registra devoluciones en `GET|POST /api/v1/compras/documentos/{registro}/devoluciones` y consulta los saldos en `GET /api/v1/compras/reportes/pendientes`. La anulación usa la ruta habitual de Notas de crédito.

### Aprobación de órdenes de compra

1. En **Compras → Órdenes de compra → Aprobaciones → Política de aprobación**, un administrador fija el total mínimo con IVA para su empresa. La regla se aplica al registrar órdenes nuevas en cualquier sucursal. USD 0 incluye todas las órdenes nuevas. Las órdenes existentes conservan su estado.
2. Asigna al revisor los permisos de lectura de **Órdenes de compra** y actualización de **Compras · Aprobar o rechazar órdenes**. El revisor debe ser distinto del creador de la orden. En la bandeja se puede buscar por número o proveedor, abrir las líneas de la orden y registrar la decisión. El rechazo exige un motivo. Si la compra continúa después de un rechazo, registra una orden nueva.
3. Las órdenes pendientes o rechazadas quedan bloqueadas para recepción y cotejo, y se excluyen de **Compras pendientes**. El historial muestra el umbral original, quién decidió, cuándo y el motivo. Si se anula una orden que entró en aprobación, la anulación también queda en el historial.

En la API v1, `GET /api/v1/compras/ordenes-compra/aprobaciones` consulta la bandeja y `GET|POST /api/v1/compras/ordenes-compra/{orden}/aprobacion` consulta el historial o registra la decisión. La política se consulta y guarda en `GET|PUT /api/v1/compras/ordenes-compra/politica-aprobacion`. Para emitir una credencial de decisión, añade `--aprobaciones` a `comercial:token compras`. Para gestionar la política por API, usa `--configurar-aprobaciones` con un administrador de la empresa.

### Solicitudes y presupuestos de compra

1. En **Compras → Órdenes de compra → Presupuestos**, un administrador asigna un monto anual con IVA a un centro de costo de la sucursal activa. El presupuesto muestra el monto asignado, reservado, comprometido y disponible. Para reducir un presupuesto, el nuevo monto tiene que cubrir las reservas y los compromisos existentes.
2. En la fila del presupuesto, elige **Nueva solicitud** e ingresa el motivo y los productos o servicios estimados. Otro usuario con permiso de aprobación revisa la solicitud en **Solicitudes**. Aprobar reserva el importe completo. Si el saldo es insuficiente, la solicitud sigue pendiente. Rechazar exige un motivo.
3. Abre la solicitud reservada y usa **Generar orden de compra**. Selecciona el proveedor y una fecha del mismo año. El sistema conserva cantidades, precios e impuestos y traslada la reserva a un compromiso sin descontar dos veces el saldo. Se aplica la política de aprobación de órdenes de la empresa. Cancelar una solicitud reservada, o rechazar o anular su orden vinculada, devuelve el saldo disponible.

El control se aplica a órdenes vinculadas a una solicitud. El comprometido incluye las órdenes que ya tengan factura. La ejecución contable contra presupuesto, la distribución de una misma solicitud entre varios centros y la obligatoriedad para compras directas quedan como fases posteriores.

La API ofrece `GET|PUT /api/v1/compras/presupuestos`, `GET|POST /api/v1/compras/solicitudes`, el detalle `/solicitudes/{solicitud}` y las acciones `/decidir`, `/generar-orden` y `/cancelar` por POST. Usa `--presupuestos` en `comercial:token compras` para gestionar montos con una cuenta administradora y `--aprobaciones` para revisar solicitudes con el permiso correspondiente. Consulta OpenAPI para las validaciones y alcances.

## Organización del código

| Ubicación | Contenido |
| --- | --- |
| `packages/modules/<módulo>/src/` | Casos de uso, reglas de dominio cuando existen y adaptadores de infraestructura. |
| `packages/modules/<módulo>/routes/` | Rutas web y API del módulo, registradas desde su proveedor. |
| `packages/modules/<módulo>/database/migrations/` | Esquema de datos propio del módulo. |
| `app/` y `routes/web.php` | Sesión, empresa activa, permisos, auditoría, navegación y servicios compartidos como Vicky. |
| `resources/views/` y `public/` | Layouts Blade, componentes compartidos y scripts del espacio de trabajo. |
| `config/` | Menús funcionales, permisos, fuentes de informes y conexiones a datos y servicios. |

Laravel Sanctum protege los endpoints que usan tokens de API. La sesión web mantiene la empresa activa y los permisos se evalúan por sección y acción. El catálogo de informes de Vicky vive en `config/vicky-reportes.php` y usa la conexión de lectura configurada para el asistente.

## Guía de navegación para Vicky

Esta tabla es parte del funcionamiento del chat. Cada fila declara un destino, su nombre de ruta Laravel y las expresiones para encontrarlo. La ruta de pestaña abre la pantalla base. Una ruta modal identifica el formulario que se abre desde esa pantalla. Usa `—` cuando la acción solo requiere abrir una pestaña.

<!-- vicky:rutas -->
| Destino | Ruta de pestaña | Ruta modal | Frases | Indicaciones |
| --- | --- | --- | --- | --- |
| Nuevo cliente | clientes.index | clientes.create | crear cliente; crear clientes; registrar cliente; registrar clientes; nuevo cliente; agregar cliente; agregar clientes | Ve a Clientes → Lista de clientes → Nuevo cliente. Busca la persona por identificación y guarda la ficha comercial. La identificación, los nombres y el contacto personal se consultan desde Personas. |
| Lista de clientes | clientes.index | — | clientes; lista de clientes; directorio de clientes | Abre Clientes → Lista de clientes para buscar por nombre, identificación o correo. |
| Cuentas por cobrar | clientes.reportes.cuentas-por-cobrar | — | cuentas por cobrar; cartera de clientes | Ve a Clientes → Reportes → Cuentas por cobrar. |
| Nuevo proveedor | proveedores.index | proveedores.create | crear proveedor; crear proveedores; registrar proveedor; registrar proveedores; nuevo proveedor; agregar proveedor; agregar proveedores | Ve a Proveedores → Lista de proveedores → Nuevo proveedor. Completa la ficha y guarda. |
| Lista de proveedores | proveedores.index | — | proveedores; lista de proveedores; directorio de proveedores | Abre el directorio de Proveedores para buscar y consultar sus fichas. |
| Cuentas por pagar | proveedores.reportes.cuentas-por-pagar | — | cuentas por pagar; cartera de proveedores; pagar proveedor; registrar pago a proveedor | Ve a Proveedores → Reportes → Cuentas por pagar. Consulta el saldo al corte y, en el corte de hoy, usa Registrar pago en la factura contabilizada. |
| Antigüedad de proveedores | proveedores.reportes.antiguedad-de-saldos | — | antigüedad de proveedores; vencimientos de proveedores | Ve a Proveedores → Reportes → Antigüedad de saldos para revisar los importes pendientes agrupados por vencimiento. |
| Comprobantes de pago a proveedores | proveedores.documentos.comprobantes-de-pago | — | comprobantes de proveedores; pagos a proveedores registrados | Ve a Proveedores → Documentos → Comprobantes de pago para consultar los egresos vinculados y su estado al corte. |
| Nuevo producto | inventarios.productos.index | inventarios.productos.create | crear producto; crear productos; registrar producto; registrar productos; nuevo producto; agregar producto; agregar productos | Ve a Inventarios → Productos → Nuevo producto. Completa la ficha del producto o servicio. |
| Productos | inventarios.productos.index | — | productos; inventario; inventarios; catálogo de productos | Abre Inventarios → Productos. |
| Existencias | inventarios.existencias.index | — | existencias; existencias por bodega; stock | Ve a Inventarios → Existencias para revisar el stock por producto y bodega. |
| Ventas | ventas.documentos.index | — | ventas; lista de ventas; documentos de venta | Ve a Ventas → Lista de ventas. |
| Factura de pruebas en el SRI | ventas.documentos.index | — | firma local de factura; firmar xml de factura; xml firmado de pruebas; enviar factura al sri de pruebas | En Ventas → Lista de ventas, selecciona la factura y abre Autorización SRI. Firma el XML y, si el envío manual está habilitado, usa Enviar al SRI de pruebas. Consulta el resultado en el historial. |
| Reportes de ventas | ventas.reportes.index | — | reportes de ventas; reporte de ventas; informe de ventas | Ve a Ventas → Reportes y aplica el período que necesitas. |
| Compras y devoluciones | compras.documentos.index | — | compras; lista de compras; documentos de compra; devolver mercadería; devolución de compra | Ve a Compras → Lista de compras. Para devolver mercadería facturada, abre Ver trazabilidad y elige Devoluciones de mercadería. |
| Órdenes y solicitudes de compra | compras.ordenes-compra.index | — | órdenes de compra; ordenes de compra; recepción parcial de compras; recibir orden de compra; recibir mercadería; cotejar factura de compra; facturar parte de una recepción; aprobar diferencias de compra; aprobar orden de compra; rechazar orden de compra; política de aprobación de compras; solicitud de compra; presupuesto de compras; presupuesto disponible | Ve a Compras → Órdenes de compra. Abre Presupuestos para consultar saldos o registrar una solicitud desde el presupuesto. Usa Solicitudes para aprobar, reservar y generar la orden. Abre Aprobaciones para revisar órdenes pendientes y Recepciones de la orden para registrar entregas y cotejar facturas. Para aprobar diferencias del cotejo se requiere permiso en Asientos contables y un motivo. |
| Compras pendientes | compras.reportes.index | — | compras pendientes; mercadería por recibir; mercadería recibida por facturar; órdenes pendientes de recibir | Ve a Compras → Reportes → Compras pendientes. Elige Por recibir o Por facturar para ver el saldo por línea de la orden. |
| Cajas y cuentas bancarias | caja-bancos.cuentas.index | — | caja y bancos; cuentas bancarias; bancos | Ve a Caja y bancos → Cajas y cuentas bancarias. |
| Conciliación bancaria | caja-bancos.conciliacion.index | — | conciliación bancaria; conciliación | Ve a Caja y bancos → Conciliación bancaria. |
| Asientos contables | contabilidad.asientos.index | — | contabilidad; asientos contables; libro de asientos | Ve a Contabilidad → Asientos contables. |
| Balance de comprobación | contabilidad.reportes.balance-comprobacion | — | balance de comprobación | Ve a Contabilidad → Reportes → Balance de comprobación. |
| Registro de activos | activos-fijos.activos.index | — | activos fijos; registro de activos | Ve a Activos fijos → Registro de activos. |
| Recursos humanos | rrhh.panel.index | — | recursos humanos; rrhh; panel de personal | Abre el panel de Recursos humanos. |
| Nuevo empleado | rrhh.empleados.index | rrhh.empleados.create | crear empleado; crear empleados; registrar empleado; registrar empleados; nuevo empleado; agregar empleado; agregar empleados | Ve a Recursos humanos → Empleados y abre el formulario de alta. |
| Empleados | rrhh.empleados.index | — | empleados; lista de empleados; directorio de personal | Ve a Recursos humanos → Empleados para consultar sus fichas. |
| Nómina | rrhh.nomina.index | — | nómina; períodos de nómina | Ve a Recursos humanos → Nómina. |
| Declaraciones tributarias | tributario.declaraciones.index | — | tributario; declaraciones tributarias; declaraciones | Ve a Tributario → Declaraciones. |
| ATS | tributario.ats.index | — | ats; anexo transaccional simplificado | Ve a Tributario → ATS. |
| Empresas | configuracion.empresas.index | — | empresas; configuración de empresas | Ve a Configuración → Empresas. |
| Sucursales | configuracion.sucursales.index | — | sucursales; configuración de sucursales | Ve a Configuración → Sucursales. |
| Importaciones | configuracion.importaciones.index | — | importaciones; datos iniciales; configurar nueva empresa; carga masiva; categorías pendientes | Ve a Configuración → Importaciones para ver qué datos están registrados y qué queda pendiente en la empresa activa. Crea las categorías y revisa cuáles necesitan plantilla contable. Las bodegas corresponden a la sucursal seleccionada. Usa Volver a Importaciones desde cualquiera de las pantallas de la lista. |
| Importar personas | configuracion.personas.importaciones.index | — | importar personas; cargar personas desde Excel; progreso de importación | Ve a Configuración → Importaciones → Importar personas. El historial se actualiza cada dos segundos y muestra la espera, la lectura del Excel, el porcentaje y las filas procesadas. Requiere administración global. |
| Importar productos | configuracion.importaciones.productos.index | — | importar productos; Excel de productos; CSV de productos; descargar plantilla; cargar catálogo inicial; importar servicios | Ve a Configuración → Importaciones → Importar productos. Descarga la plantilla Excel y sube el mismo archivo .xlsx después de llenarlo. Código, nombre y unidad son obligatorios. La plantilla también admite vencimiento de referencia, registro sanitario, fabricante, referencias externas y contenido del QR. Las unidades y marcas se vinculan por nombre y se crean si faltan en tu empresa. |
<!-- /vicky:rutas -->

En el chat, «¿Dónde creo un cliente?» devuelve los pasos y un botón. «Abre nuevo cliente» abre la pestaña y el formulario si la sesión tiene permiso. Abrir un formulario deja su envío bajo control del usuario. La orientación de esta tabla se resuelve en Aitrol y está disponible aunque Ollama o Vicky Core estén fuera de alcance.

## Desarrollo local

En Inventarios → Productos, la ficha permite editar la fecha de vencimiento de referencia, el registro sanitario, el fabricante y las referencias externas. Usa Vencimiento hasta para filtrar por fecha. Busca por registro sanitario, código externo, ID externo o contenido del QR. Los identificadores externos son texto y pueden acompañarse del nombre del sistema de origen. Una fecha distinta por lote requiere control de lotes en inventario.

Las importaciones de personas y productos se ejecutan en la cola `personas` con `php artisan queue:work personas --timeout=900 --no-interaction`. Productos acepta la plantilla Excel .xlsx o CSV UTF-8 separado por punto y coma, hasta 4 MB y 20.000 filas. Conserva los códigos existentes y registra únicamente fichas de catálogo. Las existencias iniciales se registran por bodega desde Inventarios. La API acepta el archivo en `POST /api/v1/inventarios/productos/importaciones` y consulta el resultado con `GET /api/v1/inventarios/productos/importaciones/{importacion}`.

Usa PHP 8.4 con PDO SQLite, Composer y Node.js. Copia `.env.example` a `.env` al preparar una instalación nueva. Crea `database/database.sqlite` si todavía no existe y selecciona `DB_CONNECTION=sqlite` en el entorno.

```bash
composer install
php artisan key:generate
php artisan migrate --no-interaction
npm install --ignore-scripts
npm run build
php artisan serve
```

Las rutas desplegadas se consultan con `php artisan route:list`. La especificación de API está disponible en `/docs/api` y `/openapi.json`. Las pruebas de aplicación usan PHPUnit.

## Despliegue

- Sirve el directorio `public/` por HTTPS. El proceso de PHP necesita escritura en `storage/` y `bootstrap/cache/`.
- Usa `APP_ENV=production`, `APP_DEBUG=false` y la URL HTTPS del despliegue. Configura las cookies de sesión seguras y, cuando aplique, los proxies de confianza antes de regenerar la caché de configuración.
- Mantén respaldos cifrados de la base de datos, archivos privados y clave de aplicación. Comprueba la restauración en una instancia aislada.
- Usa credenciales distintas para desplegar, operar la aplicación y consultar los informes de Vicky. La conexión de reportes debe quedar en solo lectura.
