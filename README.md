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

El inventario corresponde a los paquetes de `packages/modules/` registrados en `bootstrap/providers.php`. Algunas secciones existen en el menú y todavía dependen de una fuente de datos. Vicky puede indicar la pantalla de cartera si el usuario tiene permiso. Los importes pendientes requieren las vistas correspondientes. Pacientes exige una fuente operativa habilitada por empresa.

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
| Nuevo cliente | clientes.index | clientes.create | crear cliente; crear clientes; registrar cliente; registrar clientes; nuevo cliente; agregar cliente; agregar clientes | Ve a Clientes → Lista de clientes → Nuevo cliente. Completa la ficha y guarda. |
| Lista de clientes | clientes.index | — | clientes; lista de clientes; directorio de clientes | Abre Clientes → Lista de clientes para buscar por nombre, identificación o correo. |
| Cuentas por cobrar | clientes.reportes.cuentas-por-cobrar | — | cuentas por cobrar; cartera de clientes | Ve a Clientes → Reportes → Cuentas por cobrar. |
| Nuevo proveedor | proveedores.index | proveedores.create | crear proveedor; crear proveedores; registrar proveedor; registrar proveedores; nuevo proveedor; agregar proveedor; agregar proveedores | Ve a Proveedores → Lista de proveedores → Nuevo proveedor. Completa la ficha y guarda. |
| Lista de proveedores | proveedores.index | — | proveedores; lista de proveedores; directorio de proveedores | Abre el directorio de Proveedores para buscar y consultar sus fichas. |
| Cuentas por pagar | proveedores.reportes.cuentas-por-pagar | — | cuentas por pagar; cartera de proveedores | Ve a Proveedores → Reportes → Cuentas por pagar. |
| Nuevo producto | inventarios.productos.index | inventarios.productos.create | crear producto; crear productos; registrar producto; registrar productos; nuevo producto; agregar producto; agregar productos | Ve a Inventarios → Productos → Nuevo producto. Completa la ficha del producto o servicio. |
| Productos | inventarios.productos.index | — | productos; inventario; inventarios; catálogo de productos | Abre Inventarios → Productos. |
| Existencias | inventarios.existencias.index | — | existencias; existencias por bodega; stock | Ve a Inventarios → Existencias para revisar el stock por producto y bodega. |
| Ventas | ventas.documentos.index | — | ventas; lista de ventas; documentos de venta | Ve a Ventas → Lista de ventas. |
| Reportes de ventas | ventas.reportes.index | — | reportes de ventas; reporte de ventas; informe de ventas | Ve a Ventas → Reportes y aplica el período que necesitas. |
| Compras | compras.documentos.index | — | compras; lista de compras; documentos de compra | Ve a Compras → Lista de compras. |
| Órdenes de compra | compras.ordenes-compra.index | — | órdenes de compra; ordenes de compra | Ve a Compras → Órdenes de compra. |
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
<!-- /vicky:rutas -->

En el chat, «¿Dónde creo un cliente?» devuelve los pasos y un botón. «Abre nuevo cliente» abre la pestaña y el formulario si la sesión tiene permiso. Abrir un formulario deja su envío bajo control del usuario. La orientación de esta tabla se resuelve en Aitrol y está disponible aunque Ollama o Vicky Core estén fuera de alcance.

## Desarrollo local

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
