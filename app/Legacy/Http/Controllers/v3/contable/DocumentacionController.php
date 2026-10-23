<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\MarkdownSeguro;

class DocumentacionController extends Controller
{
    /**
     * Centro de documentacion tecnica y operativa de v3.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->autorizarAdministrador();
        $modulos = $this->modulos();
        $cantidadOpciones = 0;
        foreach ($modulos as $modulo) {
            $cantidadOpciones += count($modulo['opciones']);
        }

        $data = [
            'controlador' => 'documentacion',
            'carpeta' => 'contable',
            'submodulo_' => 'documentacion',
            'title' => 'Documentacion | Sistema v3',
            'vista' => 'v3.contable.documentacion.view_documentacion',
            'obseraudi' => 'Consulto el centro de documentacion del sistema v3',
            'fechaDocumentacion' => '15/07/2026',
            'documentosV3' => $this->documentos(),
            'modulosV3' => $modulos,
            'cantidadOpcionesV3' => $cantidadOpciones,
            'flujoV3' => [
                ['titulo' => 'Ruta', 'detalle' => 'routes/v3/contable/web.php', 'icono' => 'fas fa-route'],
                ['titulo' => 'Middleware', 'detalle' => 'Sesion, monitoreo y datos generales', 'icono' => 'fas fa-shield-alt'],
                ['titulo' => 'Controlador', 'detalle' => 'Selecciona el submodulo y prepara datos', 'icono' => 'fas fa-random'],
                ['titulo' => 'Modelo', 'detalle' => 'Eloquent, consultas y reglas de negocio', 'icono' => 'fas fa-database'],
                ['titulo' => 'Vista', 'detalle' => 'Blade dentro del layout AdminLTE v3', 'icono' => 'fas fa-desktop'],
            ],
            'reglasDesarrollo' => [
                'Mantener las nuevas rutas dentro del grupo protegido de v3.',
                'Validar toda entrada antes de ejecutar logica de negocio o consultas.',
                'Usar parametros enlazados y evitar concatenar datos del usuario en SQL.',
                'Envolver operaciones contables de varias escrituras en una transaccion.',
                'Registrar acciones relevantes en auditoria sin guardar claves ni datos sensibles.',
                'No dejar dd(), print_r(), echo de depuracion o exit en flujos productivos.',
                'Agregar pruebas para cada correccion de un proceso contable critico.',
            ],
            'pendientesDocumentacion' => [
                'Completar columnas, indices y relaciones del diccionario con el esquema MySQL del servidor.',
                'Inventario aprobado de variables por ambiente, sin incluir valores ni secretos.',
                'Ejecutar y aprobar el procedimiento de despliegue, retorno y recuperacion ante desastres.',
                'Catalogo de roles y permisos aprobado por el responsable funcional.',
                'Validar el manual funcional con casos reales y responsables de cada proceso.',
            ],
        ];

        $data['contenido'] = view($data['vista'], $data);
        $newData = array_merge(config('data', []), $data);
        config(['data' => $newData]);

        return view('v3.layouts.view_child');
    }

    /**
     * Entrega un documento permitido para mostrarlo dentro de la modal.
     *
     * @param  string  $documento
     * @return \Illuminate\Http\JsonResponse
     */
    public function detalle($documento)
    {
        $this->autorizarAdministrador();

        foreach ($this->documentos() as $configuracion) {
            if ($configuracion['id'] !== $documento) {
                continue;
            }

            $ruta = base_path($configuracion['archivo']);
            if (!is_file($ruta) || !is_readable($ruta)) {
                abort(404, 'El documento no esta disponible.');
            }

            return response()->json([
                'titulo' => $configuracion['titulo'],
                'archivo' => $configuracion['archivo'],
                'contenido' => MarkdownSeguro::render(file_get_contents($ruta)),
            ]);
        }

        abort(404, 'Documento no permitido.');
    }

    private function documentos()
    {
        $documentos = [
            [
                'titulo' => 'Indice de v3',
                'archivo' => 'docs/v3/README.md',
                'descripcion' => 'Alcance, audiencia, mapa documental y estado conocido.',
                'icono' => 'fas fa-book-open',
                'color' => 'primary',
            ],
            [
                'titulo' => 'Arquitectura y modulos',
                'archivo' => 'docs/v3/ARQUITECTURA.md',
                'descripcion' => 'Estructura de rutas, middleware, controladores, modelos, vistas y modulos.',
                'icono' => 'fas fa-project-diagram',
                'color' => 'info',
            ],
            [
                'titulo' => 'Instalacion y ambientes',
                'archivo' => 'docs/v3/INSTALACION.md',
                'descripcion' => 'Separacion entre Git y servidor, despliegue seguro y lista de verificacion.',
                'icono' => 'fas fa-server',
                'color' => 'warning',
            ],
            [
                'titulo' => 'Operacion y monitoreo',
                'archivo' => 'docs/v3/OPERACION_MONITOREO.md',
                'descripcion' => 'Panel de salud, metricas, logs, umbrales y respuesta a incidentes.',
                'icono' => 'fas fa-heartbeat',
                'color' => 'success',
            ],
            [
                'titulo' => 'Guia de desarrollo',
                'archivo' => 'docs/v3/GUIA_DESARROLLO.md',
                'descripcion' => 'Convenciones y controles para cambiar v3 de forma segura.',
                'icono' => 'fas fa-code',
                'color' => 'secondary',
            ],
            [
                'titulo' => 'Matriz de madurez',
                'archivo' => 'docs/v3/MATRIZ_MADUREZ.md',
                'descripcion' => 'Metodo de calculo, linea base y reglas para actualizar porcentajes.',
                'icono' => 'fas fa-chart-bar',
                'color' => 'dark',
            ],
            [
                'titulo' => 'Manual funcional',
                'archivo' => 'docs/v3/MANUAL_FUNCIONAL.md',
                'descripcion' => 'Opciones reales de ingreso, proposito, controles y flujos confirmados por modulo.',
                'icono' => 'fas fa-user-check',
                'color' => 'primary',
            ],
            [
                'titulo' => 'Seguridad y permisos',
                'archivo' => 'docs/v3/SEGURIDAD_PERMISOS.md',
                'descripcion' => 'Acceso, autorizacion, informacion sensible y revision de permisos.',
                'icono' => 'fas fa-user-shield',
                'color' => 'danger',
            ],
            [
                'titulo' => 'Runbook de despliegue',
                'archivo' => 'docs/v3/DESPLIEGUE.md',
                'descripcion' => 'Preparacion, publicacion, validacion y retorno entre Git y servidor.',
                'icono' => 'fas fa-cloud-upload-alt',
                'color' => 'warning',
            ],
            [
                'titulo' => 'Soporte e incidentes',
                'archivo' => 'docs/v3/SOPORTE_INCIDENTES.md',
                'descripcion' => 'Clasificacion, diagnostico, escalamiento y cierre de incidentes.',
                'icono' => 'fas fa-life-ring',
                'color' => 'success',
            ],
            [
                'titulo' => 'Diccionario inicial de datos',
                'archivo' => 'docs/v3/DICCIONARIO_DATOS.md',
                'descripcion' => 'Familias de tablas confirmadas en modelos y alcance pendiente del esquema.',
                'icono' => 'fas fa-database',
                'color' => 'info',
            ],
            [
                'titulo' => 'Pruebas automatizadas',
                'archivo' => 'docs/v3/PRUEBAS.md',
                'descripcion' => 'Alcance, ejecucion exclusiva de v3 y restricciones del ambiente.',
                'icono' => 'fas fa-vial',
                'color' => 'success',
            ],
            [
                'titulo' => 'Checklist de despliegue',
                'archivo' => 'docs/v3/CHECKLIST_DESPLIEGUE.md',
                'descripcion' => 'Formato preventivo para publicar v3 sin sobrescribir componentes del servidor.',
                'icono' => 'fas fa-clipboard-check',
                'color' => 'warning',
            ],
        ];

        foreach ($documentos as $indice => $documento) {
            $documentos[$indice]['id'] = strtolower(pathinfo($documento['archivo'], PATHINFO_FILENAME));
        }

        return $documentos;
    }

    private function autorizarAdministrador()
    {
        if (!Auth::check() || (int) Auth::user()->id_tipo_usuario !== 1) {
            abort(403, 'No tiene permisos para consultar la documentacion tecnica.');
        }
    }

    private function modulos()
    {
        return [
            [
                'nombre' => 'Configuracion del sistema',
                'opciones' => [
                    ['nombre' => 'Ubicaciones', 'ruta' => '/v3/configuracionessistema/ubicaciones', 'responsabilidad' => 'Administra paises, provincias y ciudades.'],
                    ['nombre' => 'Especialidades', 'ruta' => '/v3/configuracionessistema/especialidades', 'responsabilidad' => 'Mantiene el catalogo de especialidades.'],
                    ['nombre' => 'Procedimientos', 'ruta' => '/v3/configuracionessistema/procedimientos', 'responsabilidad' => 'Mantiene procedimientos utilizados por v3.'],
                ],
            ],
            [
                'nombre' => 'Configuraciones',
                'opciones' => [
                    ['nombre' => 'Menu', 'ruta' => '/v3/configuraciones/menu', 'responsabilidad' => 'Configura modulos visibles del menu.'],
                    ['nombre' => 'Submenus', 'ruta' => '/v3/configuraciones/subMenu', 'responsabilidad' => 'Configura opciones dentro de cada modulo.'],
                    ['nombre' => 'Permisos', 'ruta' => '/v3/configuraciones/permisos', 'responsabilidad' => 'Asigna accesos por usuario.'],
                    ['nombre' => 'Usuarios', 'ruta' => '/v3/configuraciones/usuarios', 'responsabilidad' => 'Consulta y mantiene usuarios.'],
                    ['nombre' => 'Perfil', 'ruta' => '/v3/configuraciones/perfil', 'responsabilidad' => 'Consulta el perfil del usuario activo.'],
                    ['nombre' => 'Empresas', 'ruta' => '/v3/configuraciones/empresas', 'responsabilidad' => 'Mantiene empresas y representantes.'],
                    ['nombre' => 'Roles', 'ruta' => '/v3/configuraciones/roles', 'responsabilidad' => 'Mantiene roles de usuario.'],
                    ['nombre' => 'Establecimientos', 'ruta' => '/v3/configuraciones/establecimientos', 'responsabilidad' => 'Configura sucursales y puntos relacionados.'],
                    ['nombre' => 'Catalogos', 'ruta' => '/v3/configuraciones/catalogo', 'responsabilidad' => 'Mantiene catalogos generales.'],
                    ['nombre' => 'Subcatalogos', 'ruta' => '/v3/configuraciones/subCatalogo', 'responsabilidad' => 'Mantiene valores dependientes de catalogos.'],
                    ['nombre' => 'Auditoria', 'ruta' => '/v3/configuraciones/auditoria', 'responsabilidad' => 'Consulta actividad registrada por usuario y empresa.'],
                    ['nombre' => 'Bodegas', 'ruta' => '/v3/configuraciones/bodegas', 'responsabilidad' => 'Mantiene bodegas contables.'],
                    ['nombre' => 'Planes', 'ruta' => '/v3/configuraciones/planes', 'responsabilidad' => 'Mantiene planes disponibles.'],
                    ['nombre' => 'Seleccion de plan', 'ruta' => '/v3/configuraciones/selecionarplan', 'responsabilidad' => 'Asigna planes a empresas o usuarios.'],
                ],
            ],
            [
                'nombre' => 'Utilidades',
                'opciones' => [
                    ['nombre' => 'Calendario', 'ruta' => '/v3/utilidades/calendario', 'responsabilidad' => 'Consulta eventos y actividades del calendario.'],
                    ['nombre' => 'Cambiar clave', 'ruta' => '/v3/utilidades/cambiarclave', 'responsabilidad' => 'Permite actualizar la clave del usuario.'],
                ],
            ],
            [
                'nombre' => 'Contabilidad',
                'opciones' => [
                    ['nombre' => 'Plan de cuentas', 'ruta' => '/v3/contabilidad/plandecuentas', 'responsabilidad' => 'Consulta y mantiene cuentas contables.'],
                    ['nombre' => 'Asientos descuadrados', 'ruta' => '/v3/contabilidad/descuadrados', 'responsabilidad' => 'Identifica asientos cuyo debe y haber no coinciden.'],
                    ['nombre' => 'Asientos anulados', 'ruta' => '/v3/contabilidad/anulados', 'responsabilidad' => 'Consulta asientos que fueron anulados.'],
                ],
            ],
            [
                'nombre' => 'Ventas',
                'opciones' => [
                    ['nombre' => 'Lista de ventas', 'ruta' => '/v3/ventas/listadeventas', 'responsabilidad' => 'Busca, consulta, imprime y gestiona ventas.'],
                    ['nombre' => 'Nueva venta', 'ruta' => '/v3/ventas/facturar', 'responsabilidad' => 'Registra una factura de venta.'],
                    ['nombre' => 'Factura de caja', 'ruta' => '/v3/ventas/facturadecaja', 'responsabilidad' => 'Registra facturacion desde caja.'],
                    ['nombre' => 'Venta desde orden', 'ruta' => '/v3/ventas/ventaOrden', 'responsabilidad' => 'Factura una orden existente.'],
                    ['nombre' => 'Informacion tributaria', 'ruta' => '/v3/ventas/crearInfoTributaria', 'responsabilidad' => 'Configura datos tributarios necesarios para facturar.'],
                ],
            ],
            [
                'nombre' => 'Compras',
                'opciones' => [
                    ['nombre' => 'Lista de compras', 'ruta' => '/v3/compras/listadecompras', 'responsabilidad' => 'Busca y gestiona compras registradas.'],
                    ['nombre' => 'Nueva compra', 'ruta' => '/v3/compras/addcompra', 'responsabilidad' => 'Registra una factura de compra.'],
                    ['nombre' => 'Nuevo gasto', 'ruta' => '/v3/compras/addgasto', 'responsabilidad' => 'Registra gastos sin inventario.'],
                    ['nombre' => 'Preorden', 'ruta' => '/v3/compras/preOrden', 'responsabilidad' => 'Prepara una orden antes de convertirla en compra.'],
                    ['nombre' => 'Compra desde orden', 'ruta' => '/v3/compras/nuevaFacturaOrden', 'responsabilidad' => 'Genera una compra a partir de una orden.'],
                    ['nombre' => 'Compra de activos', 'ruta' => '/v3/compras/nuevaFacturaActivos', 'responsabilidad' => 'Registra una factura asociada a activos fijos.'],
                    ['nombre' => 'Nuevo activo desde compras', 'ruta' => '/v3/compras/addActivos', 'responsabilidad' => 'Crea los activos vinculados con una compra.'],
                ],
            ],
            [
                'nombre' => 'Clientes y cobros',
                'opciones' => [
                    ['nombre' => 'Lista de clientes', 'ruta' => '/v3/clientes/listadeclientes', 'responsabilidad' => 'Consulta y mantiene clientes.'],
                    ['nombre' => 'Comprobantes de ingreso', 'ruta' => '/v3/clientes/comprobantedeingreso', 'responsabilidad' => 'Consulta cobros aplicados a documentos.'],
                    ['nombre' => 'Nuevo comprobante de ingreso', 'ruta' => '/v3/clientes/nuevo_compIngreso', 'responsabilidad' => 'Registra un cobro de cliente.'],
                    ['nombre' => 'Ingresos varios', 'ruta' => '/v3/clientes/comprobantedeingresovarios', 'responsabilidad' => 'Consulta ingresos que no corresponden a una factura.'],
                    ['nombre' => 'Nuevo ingreso varios', 'ruta' => '/v3/clientes/nuevo_compIngresoVarios', 'responsabilidad' => 'Registra un ingreso varios.'],
                    ['nombre' => 'Cruce de cuentas', 'ruta' => '/v3/clientes/crucedecuentas', 'responsabilidad' => 'Consulta cruces entre cuentas del cliente.'],
                    ['nombre' => 'Nuevo cruce de cuentas', 'ruta' => '/v3/clientes/nuevo_cruce_cuentas', 'responsabilidad' => 'Registra un cruce de cuentas.'],
                    ['nombre' => 'Cruce de valores', 'ruta' => '/v3/clientes/crucedevalores', 'responsabilidad' => 'Consulta aplicaciones de anticipos y valores.'],
                    ['nombre' => 'Nuevo cruce de valores', 'ruta' => '/v3/clientes/nuevo_cruce_valores', 'responsabilidad' => 'Aplica valores disponibles a deudas.'],
                    ['nombre' => 'Notas de credito', 'ruta' => '/v3/clientes/notadecredito', 'responsabilidad' => 'Consulta notas de credito emitidas a clientes.'],
                    ['nombre' => 'Nueva nota de credito', 'ruta' => '/v3/clientes/nuevanotadecredito', 'responsabilidad' => 'Emite una nota de credito a un cliente.'],
                    ['nombre' => 'Retenciones de clientes', 'ruta' => '/v3/clientes/retenciones', 'responsabilidad' => 'Consulta retenciones entregadas por clientes.'],
                    ['nombre' => 'Nueva retencion de cliente', 'ruta' => '/v3/clientes/nuevaretencion', 'responsabilidad' => 'Registra una retencion recibida.'],
                    ['nombre' => 'Rubros de clientes', 'ruta' => '/v3/clientes/rubros', 'responsabilidad' => 'Mantiene conceptos aplicables a clientes.'],
                ],
            ],
            [
                'nombre' => 'Proveedores y pagos',
                'opciones' => [
                    ['nombre' => 'Lista de proveedores', 'ruta' => '/v3/proveedores/listadeproveedores', 'responsabilidad' => 'Consulta y mantiene proveedores.'],
                    ['nombre' => 'Nuevo proveedor', 'ruta' => '/v3/proveedores/proveedor', 'responsabilidad' => 'Registra o edita datos de proveedor.'],
                    ['nombre' => 'Retenciones', 'ruta' => '/v3/proveedores/retenciones', 'responsabilidad' => 'Consulta retenciones emitidas.'],
                    ['nombre' => 'Nueva retencion', 'ruta' => '/v3/proveedores/nuevaretencion', 'responsabilidad' => 'Emite una retencion de proveedor.'],
                    ['nombre' => 'Saldos iniciales', 'ruta' => '/v3/proveedores/saldosiniciales', 'responsabilidad' => 'Consulta obligaciones iniciales.'],
                    ['nombre' => 'Nuevo saldo inicial', 'ruta' => '/v3/proveedores/nuevo_saldoinicial', 'responsabilidad' => 'Registra una obligacion inicial.'],
                    ['nombre' => 'Cruce de cuentas', 'ruta' => '/v3/proveedores/crucedecuentas', 'responsabilidad' => 'Consulta cruces de cuentas del proveedor.'],
                    ['nombre' => 'Nuevo cruce de cuentas', 'ruta' => '/v3/proveedores/nuevo_cruce_cuentas', 'responsabilidad' => 'Registra un cruce de cuentas.'],
                    ['nombre' => 'Rubros', 'ruta' => '/v3/proveedores/rubros', 'responsabilidad' => 'Mantiene conceptos de proveedor.'],
                    ['nombre' => 'Comprobantes de egreso', 'ruta' => '/v3/proveedores/comprobantedeegreso', 'responsabilidad' => 'Consulta pagos a proveedores.'],
                    ['nombre' => 'Comprobantes de egreso varios', 'ruta' => '/v3/proveedores/comprobantedeegresovario', 'responsabilidad' => 'Consulta pagos a otros acreedores.'],
                    ['nombre' => 'Notas de credito', 'ruta' => '/v3/proveedores/notadecredito', 'responsabilidad' => 'Consulta notas de credito de proveedores.'],
                    ['nombre' => 'Nueva nota de credito', 'ruta' => '/v3/proveedores/nuevanotadecredito', 'responsabilidad' => 'Registra una nota de credito recibida.'],
                    ['nombre' => 'Notas de debito', 'ruta' => '/v3/proveedores/notadedebito', 'responsabilidad' => 'Consulta notas de debito de proveedores.'],
                    ['nombre' => 'Nueva nota de debito', 'ruta' => '/v3/proveedores/nuevanotadedebito', 'responsabilidad' => 'Registra una nota de debito recibida.'],
                    ['nombre' => 'Cruce de valores', 'ruta' => '/v3/proveedores/crucedevalores', 'responsabilidad' => 'Consulta aplicaciones de anticipos y valores.'],
                    ['nombre' => 'Nuevo cruce de valores', 'ruta' => '/v3/proveedores/crearCruceValor', 'responsabilidad' => 'Aplica anticipos o valores a obligaciones.'],
                ],
            ],
            [
                'nombre' => 'Caja y bancos',
                'opciones' => [
                    ['nombre' => 'Lista de bancos y cajas', 'ruta' => '/v3/cajabancos/listadebancos', 'responsabilidad' => 'Consulta y mantiene cuentas financieras.'],
                    ['nombre' => 'Nueva caja o banco', 'ruta' => '/v3/cajabancos/crearcajabanco', 'responsabilidad' => 'Crea una cuenta de caja o banco.'],
                    ['nombre' => 'Transferencias', 'ruta' => '/v3/cajabancos/transferenciabancaria', 'responsabilidad' => 'Consulta transferencias bancarias.'],
                    ['nombre' => 'Nueva transferencia', 'ruta' => '/v3/cajabancos/crearTransferenciaBancaria', 'responsabilidad' => 'Registra una transferencia entre cuentas.'],
                    ['nombre' => 'Depositos', 'ruta' => '/v3/cajabancos/depositobancario', 'responsabilidad' => 'Consulta depositos bancarios.'],
                    ['nombre' => 'Nuevo deposito', 'ruta' => '/v3/cajabancos/crearDepositoBancario', 'responsabilidad' => 'Registra un deposito bancario.'],
                    ['nombre' => 'Notas de credito bancarias', 'ruta' => '/v3/cajabancos/notadecredito', 'responsabilidad' => 'Consulta notas de credito bancarias.'],
                    ['nombre' => 'Nueva nota de credito bancaria', 'ruta' => '/v3/cajabancos/crearNotaCredito', 'responsabilidad' => 'Registra una nota de credito bancaria.'],
                    ['nombre' => 'Notas de debito bancarias', 'ruta' => '/v3/cajabancos/notadedebito', 'responsabilidad' => 'Consulta notas de debito bancarias.'],
                    ['nombre' => 'Nueva nota de debito bancaria', 'ruta' => '/v3/cajabancos/notadedebitobancaria', 'responsabilidad' => 'Registra una nota de debito bancaria.'],
                    ['nombre' => 'Nueva nota de debito de caja/banco', 'ruta' => '/v3/cajabancos/nuevanotadedebitocajabanco', 'responsabilidad' => 'Registra una nota de debito asociada a caja o banco.'],
                    ['nombre' => 'Anticipos', 'ruta' => '/v3/cajabancos/registraranticipo', 'responsabilidad' => 'Registra anticipos financieros.'],
                    ['nombre' => 'Caja chica', 'ruta' => '/v3/cajabancos/cajachica', 'responsabilidad' => 'Gestiona fondos de caja chica.'],
                    ['nombre' => 'Movimientos bancarios', 'ruta' => '/v3/cajabancos/movimientosbancarios', 'responsabilidad' => 'Consulta movimientos por cuenta.'],
                    ['nombre' => 'Conciliacion bancaria', 'ruta' => '/v3/cajabancos/conciliacion', 'responsabilidad' => 'Consulta conciliaciones por periodo.'],
                    ['nombre' => 'Nueva conciliacion', 'ruta' => '/v3/cajabancos/agregarConciliacion', 'responsabilidad' => 'Registra una conciliacion bancaria.'],
                ],
            ],
            [
                'nombre' => 'Inventario',
                'opciones' => [
                    ['nombre' => 'Lista de productos', 'ruta' => '/v3/inventario/listadeproductos', 'responsabilidad' => 'Consulta y mantiene productos y sus relaciones.'],
                ],
            ],
            [
                'nombre' => 'Activos fijos',
                'opciones' => [
                    ['nombre' => 'Grupos', 'ruta' => '/v3/activosfijos/grupos', 'responsabilidad' => 'Mantiene grupos y tipos de activos.'],
                    ['nombre' => 'Subtipos', 'ruta' => '/v3/activosfijos/subtipos', 'responsabilidad' => 'Mantiene subtipos y codificacion.'],
                    ['nombre' => 'Lista de activos', 'ruta' => '/v3/activosfijos/listadeactivos', 'responsabilidad' => 'Consulta y mantiene activos fijos.'],
                    ['nombre' => 'Depreciaciones', 'ruta' => '/v3/activosfijos/depreciaciones', 'responsabilidad' => 'Consulta depreciaciones procesadas.'],
                    ['nombre' => 'Nueva depreciacion', 'ruta' => '/v3/activosfijos/crearDepreciacion', 'responsabilidad' => 'Prepara y registra una depreciacion.'],
                    ['nombre' => 'Historial de depreciacion', 'ruta' => '/v3/activosfijos/historialDepreciacion', 'responsabilidad' => 'Consulta detalle historico por activo.'],
                ],
            ],
            [
                'nombre' => 'Guias de remision',
                'opciones' => [
                    ['nombre' => 'Transportistas', 'ruta' => '/v3/guiaderemision/transportista', 'responsabilidad' => 'Consulta y mantiene transportistas.'],
                    ['nombre' => 'Lista de guias', 'ruta' => '/v3/guiaderemision/listadeguias', 'responsabilidad' => 'Busca y gestiona guias de remision.'],
                    ['nombre' => 'Nueva guia', 'ruta' => '/v3/guiaderemision/crearGuia', 'responsabilidad' => 'Registra una guia de remision.'],
                ],
            ],
            [
                'nombre' => 'Tributario',
                'opciones' => [
                    ['nombre' => 'Gestion de documentos', 'ruta' => '/v3/tributario/gestiondocumentos', 'responsabilidad' => 'Consulta estados, validaciones, logs y reprocesos.'],
                    ['nombre' => 'Declaraciones', 'ruta' => '/v3/tributario/declaraciones', 'responsabilidad' => 'Consulta informacion para declaraciones.'],
                    ['nombre' => 'ATS', 'ruta' => '/v3/tributario/ats', 'responsabilidad' => 'Genera y consulta el anexo transaccional.'],
                    ['nombre' => 'Importar documentos', 'ruta' => '/v3/tributario/importardocumentos', 'responsabilidad' => 'Carga documentos tributarios externos.'],
                ],
            ],
            [
                'nombre' => 'Importaciones',
                'opciones' => [
                    ['nombre' => 'Lista de importaciones', 'ruta' => '/v3/importaciones/listadeimportaciones', 'responsabilidad' => 'Consulta procesos de importacion.'],
                    ['nombre' => 'Nueva factura de importacion', 'ruta' => '/v3/importaciones/ingresa_factura', 'responsabilidad' => 'Registra compras y gastos de importacion.'],
                ],
            ],
            [
                'nombre' => 'Recursos humanos',
                'opciones' => [
                    ['nombre' => 'Configuraciones de nomina', 'ruta' => '/v3/recursoshumanos/configuraciones', 'responsabilidad' => 'Mantiene parametros utilizados por nomina.'],
                    ['nombre' => 'Lista de empleados', 'ruta' => '/v3/recursoshumanos/listadeempleados', 'responsabilidad' => 'Consulta empleados y datos laborales.'],
                    ['nombre' => 'Lista de roles', 'ruta' => '/v3/recursoshumanos/listaderoles', 'responsabilidad' => 'Consulta roles de pago.'],
                ],
            ],
            [
                'nombre' => 'Reportes',
                'opciones' => [
                    ['nombre' => 'Reportes de proveedores', 'ruta' => '/v3/reportes/proveedores', 'responsabilidad' => 'Cartera por pagar, saldos, deudas y pagos.'],
                    ['nombre' => 'Reportes de ventas', 'ruta' => '/v3/reportes/ventas', 'responsabilidad' => 'Consulta informes de ventas.'],
                    ['nombre' => 'Reportes contables', 'ruta' => '/v3/reportes/contables', 'responsabilidad' => 'Diario, mayor, balances y resultados.'],
                    ['nombre' => 'Reportes de clientes', 'ruta' => '/v3/reportes/clientes', 'responsabilidad' => 'Cartera, saldos y deudas de clientes.'],
                    ['nombre' => 'Reportes financieros', 'ruta' => '/v3/reportes/financieros', 'responsabilidad' => 'Balance financiero y estado de resultados.'],
                    ['nombre' => 'Reportes de activos fijos', 'ruta' => '/v3/reportes/activosfijos', 'responsabilidad' => 'Consulta informes de activos y depreciacion.'],
                ],
            ],
            [
                'nombre' => 'Gobierno y soporte',
                'opciones' => [
                    ['nombre' => 'Inicio v3', 'ruta' => '/v3/admin', 'responsabilidad' => 'Muestra el tablero inicial y accesos disponibles.'],
                    ['nombre' => 'Documentacion', 'ruta' => '/v3/documentacion', 'responsabilidad' => 'Consulta documentos tecnicos y funcionales.'],
                    ['nombre' => 'Matriz de madurez', 'ruta' => '/v3/matriz-madurez', 'responsabilidad' => 'Consulta estado, brechas y prioridades por modulo.'],
                    ['nombre' => 'Operacion y monitoreo', 'ruta' => '/v3/operacion-monitoreo', 'responsabilidad' => 'Consulta salud, actividad y errores observados.'],
                ],
            ],
        ];
    }
}
