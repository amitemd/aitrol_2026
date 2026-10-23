<?php

namespace Sis_medico\v3\contable;

class MatrizMadurez
{
    /**
     * Linea base vigente de la evaluacion v3.
     *
     * @return array
     */
    public static function dimensiones()
    {
        return [
            [
                'dimension' => 'Cobertura funcional',
                'peso' => 25,
                'madurez' => 74,
                'estado' => 'Avanzado',
                'color' => 'success',
                'hallazgo' => 'Amplia cobertura de contabilidad, ventas, compras, bancos, inventario, tributacion y reportes.',
            ],
            [
                'dimension' => 'Logica de negocio y datos',
                'peso' => 15,
                'madurez' => 62,
                'estado' => 'Control parcial',
                'color' => 'info',
                'hallazgo' => 'Procesos contables y transacciones implementados; falta versionar y documentar el esquema MySQL.',
            ],
            [
                'dimension' => 'Seguridad y autorizacion',
                'peso' => 15,
                'madurez' => 32,
                'estado' => 'En riesgo',
                'color' => 'danger',
                'hallazgo' => 'Existe autenticacion, pero la validacion de entradas y los permisos por operacion son insuficientes.',
            ],
            [
                'dimension' => 'Arquitectura y mantenibilidad',
                'peso' => 15,
                'madurez' => 36,
                'estado' => 'En desarrollo',
                'color' => 'warning',
                'hallazgo' => 'Hay clases extensas, alto acoplamiento y uso frecuente de consultas SQL crudas.',
            ],
            [
                'dimension' => 'Pruebas y calidad',
                'peso' => 10,
                'madurez' => 20,
                'estado' => 'Inicial',
                'color' => 'warning',
                'hallazgo' => 'Suite v3 iniciada con pruebas unitarias, contratos documentales y acceso protegido sin escritura en MySQL.',
            ],
            [
                'dimension' => 'Instalacion y despliegue',
                'peso' => 10,
                'madurez' => 55,
                'estado' => 'Control parcial',
                'color' => 'info',
                'hallazgo' => 'Separacion Git-servidor, runbook, checklist y preflight de solo lectura compatibles con PHP 7.1.33 y Laravel 5.4.',
            ],
            [
                'dimension' => 'Documentacion',
                'peso' => 5,
                'madurez' => 74,
                'estado' => 'Gestionado',
                'color' => 'success',
                'hallazgo' => 'Centro interno y manual funcional con 110 opciones reales, ademas de seguridad, despliegue, soporte y datos.',
            ],
            [
                'dimension' => 'Operacion y monitoreo',
                'peso' => 5,
                'madurez' => 70,
                'estado' => 'Gestionado',
                'color' => 'success',
                'hallazgo' => 'Panel, registro de peticiones, umbrales centralizados y verificacion automatizada con codigo de salida.',
            ],
        ];
    }

    /**
     * Auditoria estatica de los modulos publicados por las rutas de v3.
     *
     * El dictamen confirma que existe una implementacion identificable en el
     * repositorio; no reemplaza una prueba funcional con base de datos y SRI.
     *
     * @return array
     */
    public static function modulos()
    {
        return [
            [
                'modulo' => 'Administracion y menu',
                'ruta' => '/v3/admin',
                'grupo' => 'Plataforma',
                'madurez' => 60,
                'estado' => 'Control parcial',
                'color' => 'info',
                'dictamen' => 'Sirve con controles',
                'prioridad' => 'P2',
                'evidencia' => 'Ruta protegida, controlador y vista principal disponibles.',
                'falta' => 'Eliminar una interrupcion directa y probar permisos/menu por perfil.',
                'inicio' => 'Pruebas de acceso por rol y manejo uniforme de errores.',
            ],
            [
                'modulo' => 'Registro y planes',
                'ruta' => '/v3/planes',
                'grupo' => 'Plataforma',
                'madurez' => 52,
                'estado' => 'Control parcial',
                'color' => 'warning',
                'dictamen' => 'Sirve con restricciones',
                'prioridad' => 'P1',
                'evidencia' => 'Las rutas antiguas conservan sus URL y redirigen a Planes, Usuarios y Empresas reales; alta y consulta validan permiso.',
                'falta' => 'Probar los mantenimientos con perfiles permitidos y denegados, y autorizar individualmente todas las acciones internas de configuracion.',
                'inicio' => 'Ejecutar pruebas de acceso y operaciones CRUD con dos perfiles.',
            ],
            [
                'modulo' => 'Configuraciones contables',
                'ruta' => '/v3/configuraciones',
                'grupo' => 'Configuracion',
                'madurez' => 48,
                'estado' => 'Control parcial',
                'color' => 'warning',
                'dictamen' => 'Sirve con riesgo',
                'prioridad' => 'P1',
                'evidencia' => '62 operaciones despachadas y 18 vistas de configuracion.',
                'falta' => 'Validacion central, permisos por operacion, pruebas de cambios y auditoria consistente.',
                'inicio' => 'Proteger primero firma, secuenciales, usuarios, roles y cuentas contables.',
            ],
            [
                'modulo' => 'Configuracion del sistema',
                'ruta' => '/v3/configuracionessistema',
                'grupo' => 'Configuracion',
                'madurez' => 52,
                'estado' => 'Control parcial',
                'color' => 'info',
                'dictamen' => 'Sirve con controles',
                'prioridad' => 'P2',
                'evidencia' => 'Ruta con middleware general, controlador y tres vistas.',
                'falta' => 'Pruebas de autorizacion administrativa y validacion funcional por opcion.',
                'inicio' => 'Inventariar cambios sensibles y exigir perfil administrador.',
            ],
            [
                'modulo' => 'Utilidades',
                'ruta' => '/v3/utilidades',
                'grupo' => 'Soporte',
                'madurez' => 42,
                'estado' => 'Fragil',
                'color' => 'warning',
                'dictamen' => 'Sirve con riesgo',
                'prioridad' => 'P1',
                'evidencia' => '37 operaciones, carga/descarga de archivos y seis vistas.',
                'falta' => 'Endurecer archivos, dividir una clase de mas de 3000 lineas y retirar cortes de ejecucion.',
                'inicio' => 'Validar nombre, extension, tamano, ruta y autorizacion de cada archivo.',
            ],
            [
                'modulo' => 'Contabilidad',
                'ruta' => '/v3/contabilidad',
                'grupo' => 'Nucleo financiero',
                'madurez' => 38,
                'estado' => 'Fragil',
                'color' => 'danger',
                'dictamen' => 'Sirve con riesgo',
                'prioridad' => 'P1',
                'evidencia' => 'Procesos contables y transacciones implementados en una clase de mas de 6300 lineas.',
                'falta' => 'Pruebas de asientos/cierres, validacion, menos SQL crudo y eliminacion de salidas directas.',
                'inicio' => 'Cubrir asiento, mayor, cierre y anulacion con pruebas sobre una base aislada.',
            ],
            [
                'modulo' => 'Proveedores',
                'ruta' => '/v3/proveedores',
                'grupo' => 'Nucleo financiero',
                'madurez' => 46,
                'estado' => 'Control parcial',
                'color' => 'warning',
                'dictamen' => 'Sirve con riesgo',
                'prioridad' => 'P2',
                'evidencia' => '23 vistas y procesos de deuda, pagos, cruces y comprobantes.',
                'falta' => 'Pruebas de saldos, validacion de entradas y reduccion de consultas SQL crudas.',
                'inicio' => 'Conciliar saldo proveedor contra compras, pagos y retenciones.',
            ],
            [
                'modulo' => 'Clientes',
                'ruta' => '/v3/clientes',
                'grupo' => 'Nucleo financiero',
                'madurez' => 55,
                'estado' => 'Control parcial',
                'color' => 'info',
                'dictamen' => 'Sirve con riesgo',
                'prioridad' => 'P2',
                'evidencia' => '77 operaciones despachadas, 21 vistas y transacciones para cobros/cruces.',
                'falta' => 'Pruebas de cartera, permisos por accion y limpieza de consultas/salidas heredadas.',
                'inicio' => 'Conciliar cartera, anticipos, cruces y notas de credito.',
            ],
            [
                'modulo' => 'Ventas',
                'ruta' => '/v3/ventas',
                'grupo' => 'Nucleo financiero',
                'madurez' => 40,
                'estado' => 'Fragil',
                'color' => 'danger',
                'dictamen' => 'Sirve con riesgo',
                'prioridad' => 'P1',
                'evidencia' => '36 operaciones, ocho vistas y transacciones para facturacion y cartera.',
                'falta' => 'Pruebas de totales/impuestos, retirar cortes de ejecucion y desacoplar la clase principal.',
                'inicio' => 'Probar venta completa: calculo, asiento, inventario, electronica y anulacion.',
            ],
            [
                'modulo' => 'Compras',
                'ruta' => '/v3/compras',
                'grupo' => 'Nucleo financiero',
                'madurez' => 38,
                'estado' => 'Fragil',
                'color' => 'danger',
                'dictamen' => 'Sirve con riesgo',
                'prioridad' => 'P1',
                'evidencia' => '54 operaciones y procesos transaccionales de compra, pagos y retenciones.',
                'falta' => 'Eliminar depuracion activa, probar impuestos/saldos y uniformar rollback y errores.',
                'inicio' => 'Probar compra completa y anulacion sin descuadrar contabilidad o inventario.',
            ],
            [
                'modulo' => 'Caja y bancos',
                'ruta' => '/v3/cajabancos',
                'grupo' => 'Nucleo financiero',
                'madurez' => 40,
                'estado' => 'Fragil',
                'color' => 'danger',
                'dictamen' => 'Sirve con riesgo',
                'prioridad' => 'P1',
                'evidencia' => '77 operaciones, 20 vistas y transacciones para ingresos, egresos y conciliacion.',
                'falta' => 'Retirar salidas directas y probar saldos, conciliacion y reversos concurrentes.',
                'inicio' => 'Pruebas de ingreso/egreso, conciliacion y anulacion con control de saldos.',
            ],
            [
                'modulo' => 'Inventario',
                'ruta' => '/v3/inventario',
                'grupo' => 'Operacion',
                'madurez' => 44,
                'estado' => 'Control parcial',
                'color' => 'warning',
                'dictamen' => 'Sirve con riesgo',
                'prioridad' => 'P1',
                'evidencia' => 'Cinco vistas y modelos para movimientos, kardex, series y bodegas.',
                'falta' => 'Pruebas de costo/stock, concurrencia y trazabilidad completa de movimientos.',
                'inicio' => 'Cuadrar existencia fisica, kardex y contabilidad por producto/bodega.',
            ],
            [
                'modulo' => 'Activos fijos',
                'ruta' => '/v3/activosfijos',
                'grupo' => 'Operacion',
                'madurez' => 46,
                'estado' => 'Control parcial',
                'color' => 'warning',
                'dictamen' => 'Sirve con riesgo',
                'prioridad' => 'P2',
                'evidencia' => '43 operaciones, 11 vistas y procesos transaccionales de activos/depreciacion.',
                'falta' => 'Quitar depuracion en excepciones y probar alta, depreciacion, baja y asiento.',
                'inicio' => 'Conciliar auxiliar de activos con depreciacion acumulada y mayor.',
            ],
            [
                'modulo' => 'Guias de remision',
                'ruta' => '/v3/guiaderemision',
                'grupo' => 'Documentos electronicos',
                'madurez' => 50,
                'estado' => 'Control parcial',
                'color' => 'info',
                'dictamen' => 'Sirve con riesgo',
                'prioridad' => 'P1',
                'evidencia' => 'Cuatro vistas y flujo transaccional de cabecera, detalle y emision.',
                'falta' => 'Pruebas XSD/SRI, estados de reintento y validacion de transportista/destinatario.',
                'inicio' => 'Probar crear, validar XSD, firmar, enviar, recuperar y anular.',
            ],
            [
                'modulo' => 'Tributario',
                'ruta' => '/v3/tributario',
                'grupo' => 'Documentos electronicos',
                'madurez' => 42,
                'estado' => 'Fragil',
                'color' => 'danger',
                'dictamen' => 'Sirve con riesgo',
                'prioridad' => 'P1',
                'evidencia' => 'Gestion de comprobantes, ATS, retenciones y estados electronicos implementada.',
                'falta' => 'Pruebas SRI/XSD, retirar interrupciones, controlar secuenciales y dividir una clase extensa.',
                'inicio' => 'Estabilizar secuenciales, idempotencia, consulta SRI y reproceso seguro.',
            ],
            [
                'modulo' => 'Reportes',
                'ruta' => '/v3/reportes',
                'grupo' => 'Consulta',
                'madurez' => 38,
                'estado' => 'Fragil',
                'color' => 'danger',
                'dictamen' => 'Sirve con riesgo',
                'prioridad' => 'P1',
                'evidencia' => '20 vistas especializadas y un controlador de 776 lineas.',
                'falta' => 'Eliminar depuracion activa, validar filtros y probar totales contra contabilidad.',
                'inicio' => 'Certificar balance, resultados, cartera y tributarios con casos cerrados.',
            ],
            [
                'modulo' => 'Recursos humanos',
                'ruta' => '/v3/recursoshumanos',
                'grupo' => 'Operacion',
                'madurez' => 35,
                'estado' => 'En desarrollo',
                'color' => 'danger',
                'dictamen' => 'Parcial',
                'prioridad' => 'P2',
                'evidencia' => 'Cuatro vistas y modelos de nomina/configuraciones disponibles.',
                'falta' => 'Cobertura funcional y pruebas de nomina, contratos, permisos y calculos laborales.',
                'inicio' => 'Definir alcance real y certificar un ciclo completo de nomina.',
            ],
            [
                'modulo' => 'Importaciones',
                'ruta' => '/v3/importaciones',
                'grupo' => 'Integracion',
                'madurez' => 40,
                'estado' => 'Fragil',
                'color' => 'warning',
                'dictamen' => 'Sirve con riesgo',
                'prioridad' => 'P2',
                'evidencia' => 'Seis operaciones y dos vistas para cargas de compras/gastos.',
                'falta' => 'Validar archivos, idempotencia, rollback, limites y reporte fila por fila.',
                'inicio' => 'Probar reimportacion del mismo archivo sin duplicar datos.',
            ],
            [
                'modulo' => 'Opciones de documentos',
                'ruta' => '/v3/opcionesdocumentos',
                'grupo' => 'Documentos electronicos',
                'madurez' => 38,
                'estado' => 'Fragil',
                'color' => 'danger',
                'dictamen' => 'Sirve con riesgo',
                'prioridad' => 'P1',
                'evidencia' => 'Controlador de opciones electronicas conectado al emisor existente.',
                'falta' => 'Eliminar salida directa, documentar estados y probar idempotencia/recuperacion.',
                'inicio' => 'Unificar este flujo con gestion tributaria y una sola maquina de estados.',
            ],
            [
                'modulo' => 'APIs v3',
                'ruta' => '/api/v1 y /v1',
                'grupo' => 'Integracion',
                'madurez' => 55,
                'estado' => 'Control parcial',
                'color' => 'warning',
                'dictamen' => 'Sirve con restricciones',
                'prioridad' => 'P1',
                'evidencia' => 'Contrato de 12 claves, sesion, JSON, aislamiento empresa/tipo y comando de certificacion de solo lectura; prototipos sin rutas activas.',
                'falta' => 'Ejecutar v3:api-check y la suite v3 en el servidor; definir token solo si aparecen consumidores externos.',
                'inicio' => 'Ejecutar php artisan v3:api-check --json antes del despliegue y conservar la salida como evidencia.',
            ],
            [
                'modulo' => 'Documentacion interna',
                'ruta' => '/v3/documentacion',
                'grupo' => 'Gobierno',
                'madurez' => 82,
                'estado' => 'Gestionado',
                'color' => 'success',
                'dictamen' => 'Sirve',
                'prioridad' => 'P3',
                'evidencia' => 'Centro protegido, render seguro y pruebas unitarias de documentos obligatorios.',
                'falta' => 'Mantener responsables, fecha de revision y evidencia funcional por modulo.',
                'inicio' => 'Vincular cada correccion prioritaria con su prueba y procedimiento.',
            ],
            [
                'modulo' => 'Operacion y monitoreo',
                'ruta' => '/v3/operacion-monitoreo',
                'grupo' => 'Gobierno',
                'madurez' => 75,
                'estado' => 'Gestionado',
                'color' => 'success',
                'dictamen' => 'Sirve',
                'prioridad' => 'P3',
                'evidencia' => 'Panel protegido, umbrales centralizados y pruebas de analizadores/evaluadores.',
                'falta' => 'Prueba operativa con alertas reales, respaldo verificado y tendencia historica.',
                'inicio' => 'Configurar marcadores/umbrales del servidor y ensayar un incidente.',
            ],
            [
                'modulo' => 'Matriz de madurez',
                'ruta' => '/v3/matriz-madurez',
                'grupo' => 'Gobierno',
                'madurez' => 82,
                'estado' => 'Gestionado',
                'color' => 'success',
                'dictamen' => 'Sirve',
                'prioridad' => 'P3',
                'evidencia' => 'Calculo aislado, ruta protegida, documentacion y pruebas unitarias.',
                'falta' => 'Reevaluacion periodica con pruebas funcionales y responsable por modulo.',
                'inicio' => 'Actualizar dictamen solo con evidencia reproducible.',
            ],
        ];
    }

    /**
     * Registro versionado de revisiones y cambios que afectan la matriz.
     * Una mejora solo eleva la madurez cuando deja evidencia verificable.
     *
     * @return array
     */
    public static function revisiones()
    {
        return [
            [
                'fecha' => '2026-07-15 09:00:00',
                'modulo' => 'Todos los modulos v3',
                'tipo' => 'Auditoria',
                'cambio' => 'Inventario estatico inicial de rutas, controladores, vistas, pruebas y riesgos.',
                'evidencia' => '23 modulos clasificados con madurez, dictamen, brecha y prioridad P0-P3.',
                'resultado' => 'Se establece la linea base; no implica certificacion funcional con datos reales.',
                'madurez_anterior' => null,
                'madurez_nueva' => 46,
            ],
            [
                'fecha' => '2026-07-15 10:00:00',
                'modulo' => 'Documentacion interna',
                'tipo' => 'Modificacion',
                'cambio' => 'Manual y mapa funcional reemplazados por opciones reales de ingreso.',
                'evidencia' => '110 entradas documentadas y contrastadas con los controladores v3.',
                'resultado' => 'La madurez sube de 78% a 82%; falta validar cada flujo con responsables y casos reales.',
                'madurez_anterior' => 78,
                'madurez_nueva' => 82,
            ],
            [
                'fecha' => '2026-07-15 11:00:00',
                'modulo' => 'APIs v3',
                'tipo' => 'Revision y priorizacion',
                'cambio' => 'Se selecciona como primer modulo del plan de mejora.',
                'evidencia' => 'Endpoints sin autenticacion propia, operaciones GET y respuestas con echo/exit.',
                'resultado' => 'Permanece en 20% y P0 hasta cerrar o autenticar los endpoints y probar sus contratos.',
                'madurez_anterior' => 20,
                'madurez_nueva' => 20,
            ],
            [
                'fecha' => '2026-07-15 11:30:00',
                'modulo' => 'Matriz de madurez',
                'tipo' => 'Modificacion',
                'cambio' => 'Se incorpora historial obligatorio de revisiones y plan ordenado de mejora.',
                'evidencia' => 'La matriz muestra fecha, evidencia, resultado y variacion de madurez por cambio.',
                'resultado' => 'La madurez sube de 78% a 82%; cada cambio futuro debe agregar un registro.',
                'madurez_anterior' => 78,
                'madurez_nueva' => 82,
            ],
            [
                'fecha' => '2026-07-15 12:00:00',
                'modulo' => 'APIs v3',
                'tipo' => 'Revision tecnica',
                'cambio' => 'Se inspeccionan rutas, carga del proveedor, controladores y consumidores internos.',
                'evidencia' => 'Solo /api/v1/tributario esta cargado; no filtra empresa ni exige autenticacion. api.php no es cargado y el correo tiene namespace y datos fijos.',
                'resultado' => 'Permanece en 20%. Primero se revisaran logs y consumidores; no se activaran ni eliminaran endpoints en esta etapa.',
                'madurez_anterior' => 20,
                'madurez_nueva' => 20,
            ],
            [
                'fecha' => '2026-07-15 15:53:39',
                'modulo' => 'Registro y planes',
                'tipo' => 'Correccion preventiva',
                'cambio' => 'Se protegen las rutas, el alta queda solo por POST y se restringe la respuesta de busqueda.',
                'evidencia' => 'Middleware general activo, parametros acotados, validacion de entradas y seleccion explicita de campos de usuario.',
                'resultado' => 'Sube de 28% a 45%; falta permiso administrativo y validar la vigencia de las vistas de registro.',
                'madurez_anterior' => 28,
                'madurez_nueva' => 45,
            ],
            [
                'fecha' => '2026-07-15 15:53:40',
                'modulo' => 'APIs v3',
                'tipo' => 'Correccion preventiva',
                'cambio' => 'El resumen tributario activo se autentica por sesion y se aisla por empresa.',
                'evidencia' => 'Todas las consultas del endpoint filtran id_empresa y responden 403 cuando no existe empresa activa.',
                'resultado' => 'Sube de 20% a 35%; continua P0 hasta disponer de pruebas de contrato y autenticacion para clientes externos.',
                'madurez_anterior' => 20,
                'madurez_nueva' => 35,
            ],
            [
                'fecha' => '2026-07-15 16:02:30',
                'modulo' => 'Registro y planes',
                'tipo' => 'Integracion funcional',
                'cambio' => 'Se reemplazan referencias a vistas inexistentes por las pantallas reales de configuracion v3.',
                'evidencia' => 'Las URL de compatibilidad redirigen a planes, usuarios y empresas; el flujo conserva sesion, permisos y validacion.',
                'resultado' => 'Sube de 45% a 52%; deja de depender de tres vistas ausentes y queda pendiente la prueba CRUD por perfil.',
                'madurez_anterior' => 45,
                'madurez_nueva' => 52,
            ],
            [
                'fecha' => '2026-07-15 16:12:12',
                'modulo' => 'APIs v3',
                'tipo' => 'Correccion de seguridad compatible',
                'cambio' => 'Se normaliza el acceso sin sesion, se aislan estados por empresa y tipo, y se neutralizan los prototipos inactivos.',
                'evidencia' => 'El GET activo mantiene su estructura; responde 401 JSON, evita cache y filtra por emisor/maestro. Correo y emision responden 410; la logica privada de emision se conserva sin invocarse.',
                'resultado' => 'Sube de 35% a 50% y pasa de P0 a P1; falta ejecutar el contrato autenticado contra datos de dos empresas.',
                'madurez_anterior' => 35,
                'madurez_nueva' => 50,
            ],
            [
                'fecha' => '2026-07-15 16:22:56',
                'modulo' => 'APIs v3',
                'tipo' => 'Preparacion para produccion',
                'cambio' => 'Se incorpora un certificador de contrato, esquema, maestros, muestra de dos empresas y aislamiento documental.',
                'evidencia' => 'El comando v3:api-check es de solo lectura, esta registrado y no genera XML, firma, correo ni solicitudes al SRI.',
                'resultado' => 'Sube de 50% a 55%; queda condicionado a ejecutar el comando y PHPUnit v3 en el servidor antes de aprobar produccion.',
                'madurez_anterior' => 50,
                'madurez_nueva' => 55,
            ],
            [
                'fecha' => '2026-07-15 16:34:26',
                'modulo' => 'APIs v3',
                'tipo' => 'Correccion de sincronizacion',
                'cambio' => 'Se sincroniza la ficha visible del modulo con la ultima revision registrada.',
                'evidencia' => 'La fuente consumida por la pantalla cambia de 50% a 55% y se agrega una prueba contra futuras diferencias.',
                'resultado' => 'La pantalla muestra 55%; la madurez se mantiene en 55% hasta ejecutar la certificacion en el servidor.',
                'madurez_anterior' => 55,
                'madurez_nueva' => 55,
            ],
        ];
    }

    /**
     * Agrega a cada modulo la ultima revision registrada.
     *
     * @param  array  $modulos
     * @param  array  $revisiones
     * @return array
     */
    public static function aplicarRevisiones(array $modulos, array $revisiones)
    {
        foreach ($modulos as $indice => $modulo) {
            $modulos[$indice]['ultima_revision'] = '2026-07-15 09:00:00';
            $modulos[$indice]['ultimo_resultado'] = 'Linea base de auditoria estatica.';

            foreach ($revisiones as $revision) {
                if ($revision['modulo'] !== $modulo['modulo']) {
                    continue;
                }
                if ($revision['fecha'] >= $modulos[$indice]['ultima_revision']) {
                    $modulos[$indice]['ultima_revision'] = $revision['fecha'];
                    $modulos[$indice]['ultimo_resultado'] = $revision['resultado'];
                }
            }
        }

        return $modulos;
    }

    /**
     * Orden recomendado para reducir primero el riesgo mas alto.
     *
     * @return array
     */
    public static function planMejora()
    {
        return [
            [
                'orden' => 1,
                'modulo' => 'APIs v3',
                'prioridad' => 'P1',
                'objetivo' => 'Ejecutar v3:api-check con dos empresas, conservar el contrato y definir token solo si existen consumidores externos.',
            ],
            [
                'orden' => 2,
                'modulo' => 'Registro y planes',
                'prioridad' => 'P0',
                'objetivo' => 'Mover altas al grupo protegido, aplicar autorizacion y separar consultas de operaciones de escritura.',
            ],
            [
                'orden' => 3,
                'modulo' => 'Tributario',
                'prioridad' => 'P1',
                'objetivo' => 'Certificar secuenciales, idempotencia, XSD, consulta SRI y reproceso de documentos.',
            ],
            [
                'orden' => 4,
                'modulo' => 'Contabilidad, Ventas, Compras y Caja/Bancos',
                'prioridad' => 'P1',
                'objetivo' => 'Crear pruebas integrales, retirar interrupciones y proteger saldos, asientos, anulaciones y concurrencia.',
            ],
        ];
    }

    /**
     * Resume el inventario sin consultar Laravel ni MySQL.
     *
     * @param  array  $modulos
     * @return array
     */
    public static function resumirModulos(array $modulos)
    {
        $totalMadurez = 0;
        $prioridades = ['P0' => 0, 'P1' => 0, 'P2' => 0, 'P3' => 0];
        $dictamenes = [];

        foreach ($modulos as $modulo) {
            $totalMadurez += $modulo['madurez'];
            if (isset($prioridades[$modulo['prioridad']])) {
                $prioridades[$modulo['prioridad']]++;
            }
            if (!isset($dictamenes[$modulo['dictamen']])) {
                $dictamenes[$modulo['dictamen']] = 0;
            }
            $dictamenes[$modulo['dictamen']]++;
        }

        return [
            'total' => count($modulos),
            'promedio' => count($modulos) > 0 ? round($totalMadurez / count($modulos)) : 0,
            'prioridades' => $prioridades,
            'dictamenes' => $dictamenes,
        ];
    }

    /**
     * Calcula aportes sin depender de Laravel ni de MySQL.
     *
     * @param  array  $dimensiones
     * @return array
     */
    public static function calcular(array $dimensiones)
    {
        $total = 0;
        foreach ($dimensiones as $indice => $dimension) {
            $aporte = ($dimension['peso'] * $dimension['madurez']) / 100;
            $dimensiones[$indice]['aporte'] = $aporte;
            $total += $aporte;
        }

        return [
            'dimensiones' => $dimensiones,
            'total' => $total,
            'total_redondeado' => round($total),
        ];
    }
}
