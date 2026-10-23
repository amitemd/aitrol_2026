# Arquitectura de v3

## Resumen

V3 es un modulo integrado en una aplicacion Laravel de mayor tamano. No es una aplicacion independiente. Utiliza MySQL, sesiones Laravel, Eloquent/Query Builder, vistas Blade y el tema AdminLTE.

El servidor utiliza PHP 7.1.33 y Laravel 5.4. Todo codigo nuevo de v3 debe conservar compatibilidad con esas versiones. Los manifiestos de Composer y las dependencias instaladas se conservan en el servidor y no forman parte de la copia Git de desarrollo.

## Ubicacion de componentes

| Componente | Ruta |
|---|---|
| Modelos y reglas de negocio | `app/v3/contable` |
| Controladores | `app/Http/Controllers/v3/contable` |
| Middleware | `app/Http/Middleware/v3` |
| Observadores | `app/Observers/v3` |
| Vistas | `resources/views/v3` |
| Rutas web | `routes/v3/contable/web.php` |
| Rutas API declaradas | `routes/v3/contable/api.php` |
| Idiomas | `resources/lang/{idioma}/v3` |

## Flujo de una peticion protegida

1. `RouteServiceProvider` carga `routes/v3/contable/web.php` con los middleware `web` y `cors`.
2. El grupo v3 ejecuta `operacion.v3` para medir estado HTTP y duracion.
3. El middleware `general` confirma la sesion y prepara empresa, usuario, permisos, menu y configuraciones.
4. El controlador selecciona la accion o submodulo.
5. Los modelos ejecutan reglas de negocio y consultas MySQL.
6. El controlador configura el contenido y devuelve `v3.layouts.view_child`.
7. El layout compone encabezado, menu, contenido, pie, variables y JavaScript.

## Modulos principales

- Administracion y configuraciones.
- Contabilidad y reportes financieros.
- Ventas, clientes y cobros.
- Compras, proveedores y pagos.
- Caja, bancos y conciliacion.
- Inventario, bodegas y kardex.
- Activos fijos y depreciacion.
- Tributario, ATS y documentos electronicos.
- Recursos humanos, importaciones y guias de remision.

## Datos y transacciones

Los modelos usan Eloquent, Query Builder y SQL crudo. Las operaciones que escriben varias tablas suelen utilizar `beginTransaction`, `commit` y `rollBack`. La tabla `new_db_auditorias` conserva actividad por empresa y usuario.

Riesgos conocidos:

- Clases de negocio con miles de lineas.
- Consultas SQL crudas y algunas variables concatenadas.
- Reglas, persistencia y presentacion parcialmente acopladas.
- Ausencia del esquema MySQL versionado en esta copia.

## Seguridad

Las rutas internas usan el middleware `general`. Los paneles de documentacion y monitoreo agregan una comprobacion administrativa. Esta proteccion no reemplaza una matriz formal de permisos por cada operacion de negocio.

Nunca se deben registrar claves, tokens, certificados, XML confidenciales o contenido completo de solicitudes en logs operativos.
