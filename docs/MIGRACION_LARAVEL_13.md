# Migración de Sucursales IECED a Laravel 13

## Arquitectura adoptada

```text
Solicitud HTTP
    │
    ├── Rutas modernas ──► Catálogo visual / salud / nuevas funciones
    │
    └── RouteServiceProvider heredado
            │
            ├── 81 archivos de rutas en routes/legacy
            ├── middleware moderno de Laravel 13
            └── controladores y modelos Sis_medico en app/Legacy
                        │
                        ├── Eloquent / base MySQL existente
                        └── capa de compatibilidad
                              ├── Excel/PHPExcel → PhpSpreadsheet
                              ├── PDF, formularios y código de barras
                              └── respuesta 501 para código ausente en el origen
```

Esta separación permite que las funciones nuevas vivan bajo `App\` y que el sistema histórico permanezca bajo `Sis_medico\`, sin una reescritura masiva que cambie reglas de negocio silenciosamente.

## Inventario migrado

| Elemento | Cantidad | Ubicación nueva |
|---|---:|---|
| Archivos PHP heredados | 1.613 | `app/Legacy` |
| Archivos de rutas heredadas | 93 | `routes/legacy` |
| Rutas registradas | 5.510 | Laravel Router |
| Vistas Blade | 3.199 | `resources/views` |
| Migraciones históricas | 21 | `database/migrations/legacy` |
| Módulos visualizados | 34 | `/mapa-modulos` |

Los módulos están explicados individualmente en el catálogo visual. Incluyen clínica, historia clínica, hospitalización, laboratorio, contabilidad, inventario, facturación, archivo plano, activos fijos, recursos humanos, turnero, soporte y operaciones.

## Decisiones de compatibilidad

### Rutas y namespaces

El `RouteServiceProvider` histórico conserva los nombres de controladores y URLs. Sus rutas apuntan ahora a `routes/legacy`. Los middleware se registran mediante `bootstrap/app.php`, como corresponde a Laravel 13.

### Reportes Excel

El origen usa extensamente `Excel::create`, `Excel::load` y clases globales `PHPExcel_*`, APIs que ya no existen en las versiones modernas. La carpeta `app/Compatibility/Excel` traduce esas operaciones a PhpSpreadsheet sin modificar miles de llamadas de negocio.

### Configuración y secretos

Correo, tokens, base de datos y URLs externas deben configurarse en `.env`. No se copió la contraseña SMTP incrustada en el proyecto anterior. Las configuraciones de gateway, idiomas y monitoreo V3 sí fueron trasladadas usando variables de entorno.

### Procesos programados

Los comandos `custom:command`, `v3:api-check`, `v3:deploy-check` y `v3:health-check` están registrados. También se conservaron las tareas de porcentaje de laboratorio y documentos electrónicos. La llamada de documentos electrónicos usa el cliente HTTP de Laravel en lugar de ejecutar `wget`.

## Limitaciones encontradas en el código fuente

El proyecto recibido no contiene `composer.json`, `vendor`, `storage`, `bootstrap`, el esquema completo de base de datos ni 14 controladores referenciados por sus rutas. Para que esas rutas no provoquen un error fatal, Laravel responde con estado `501` y muestra exactamente la acción faltante.

Controladores ausentes en el origen:

- `CamillaManagementController`
- `FinancieroController`
- `FormularioController`
- `ReporteComisionesController`
- `BiopsiaPacienteController`
- `hc4\ResultadoBiopsiasController`
- `hc4\EgresoController`
- `archivo_plano\CrudMedicamentosController`
- `archivo_plano\CrudInsumosController`
- `PaginaController`
- `contable\LiquidacionCompraController`
- `contable\MantenimientoPrestamosController`
- `contable\ReporteFlujoController`
- `contable\PrecioProductoAprobadoController`

No es posible reproducir la lógica que no estaba en el código entregado. Si se consiguen esos archivos desde otra copia del sistema, deben incorporarse bajo su namespace y retirarse sus alias de `LegacyCompatibilityServiceProvider`.

## Base de datos

Para validar toda la operación es necesario restaurar una copia de la base utilizada por Sucursales IECED y configurar sus credenciales en `.env`. Las migraciones heredadas se conservaron como referencia, pero no se ejecutan automáticamente porque son parciales y podrían alterar una base existente.

Antes de conectar producción:

1. Restaurar una copia en un entorno de pruebas.
2. Configurar `DB_*` en `.env`.
3. Ejecutar `php artisan config:clear`.
4. Probar inicio de sesión y cada flujo crítico con datos representativos.
5. Validar reportes Excel/PDF, SRI, correo, Google API, archivos y tareas programadas.
6. Comparar saldos y documentos generados con el sistema anterior.

## Verificaciones ejecutadas

- Arranque de Laravel 13 con PHP 8.4.
- Registro del catálogo, proveedores, middleware, comandos y tareas.
- Análisis sintáctico de todos los PHP de `app`, `routes`, `config` y migraciones: cero errores.
- Pruebas PHPUnit del catálogo, navegación y compatibilidad Excel.
- Compilación de Tailwind CSS 4 y Vite para producción.
- Formato de los archivos modificados con Laravel Pint.

La equivalencia final de datos e integraciones externas solo puede certificarse después de conectar la base y los servicios reales.
