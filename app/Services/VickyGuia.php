<?php

namespace App\Services;

use Illuminate\Support\Str;

final class VickyGuia
{
    private const MODULOS = [
        'sales' => ['ventas.reportes.index', ['ventas', 'facturación de ventas'], [
            'quote' => ['Cotización', 'cotizaciones'],
            'order' => ['Pedido', 'pedidos de venta'],
            'invoice' => ['Factura de venta', 'facturar una venta'],
            'credit_note' => ['Nota de crédito de venta'],
            'payment' => ['Pago de una venta'],
            'delivery' => ['Entrega al cliente'],
            'customer' => ['Ficha de cliente', 'clientes', 'cliente', 'directorio de clientes'],
            'return' => ['Devolución de venta', 'devoluciones de ventas'],
        ]],
        'purchasing' => ['compras.reportes.index', ['compras', 'abastecimiento'], [
            'purchase_request' => ['Solicitud de compra'],
            'purchase_order' => ['Orden de compra', 'órdenes de compra'],
            'receipt' => ['Recepción de compras', 'recepción de mercadería'],
            'supplier_invoice' => ['Factura de proveedor', 'factura de compra', 'facturas de proveedores'],
            'payment' => ['Pago de una compra'],
            'supplier' => ['Ficha de proveedor', 'proveedores', 'proveedor', 'directorio de proveedores'],
            'return' => ['Devolución de compra', 'devoluciones de compras'],
        ]],
        'inventory' => ['inventarios.productos.index', ['inventario', 'inventarios', 'bodega', 'kardex'], [
            'product' => ['Producto', 'productos'],
            'receipt' => ['Recepción de inventario'],
            'stock' => ['Existencias', 'stock', 'kárdex', 'kardex'],
            'stock_adjustment' => ['Ajuste de existencias', 'ajustar existencias', 'ajuste de inventario', 'ajustar el inventario', 'ajuste de stock', 'ajustar el stock'],
            'stock_count' => ['Conteo de inventario', 'conteo físico'],
            'transfer' => ['Traslado', 'transferencia entre bodegas', 'trasladar productos'],
            'delivery' => ['Entrega de inventario'],
        ]],
        'accounting' => ['contabilidad.asientos.index', ['contabilidad'], [
            'journal_entry' => ['Asiento contable', 'asientos contables', 'asiento de diario', 'libro diario', 'libro de diario', 'libro de asientos', 'asiento', 'debe y haber'],
            'ledger' => ['Libro mayor', 'mayor contable', 'mayorización'],
            'reconciliation' => ['Conciliación contable'],
            'trial_balance' => ['Balance de comprobación', 'balance general'],
            'close' => ['Cierre contable', 'cerrar un periodo contable', 'cerrar el periodo contable', 'cierre de mes'],
            'invoice' => ['Factura contabilizada'],
            'credit_note' => ['Nota de crédito contabilizada'],
            'payment' => ['Pago contabilizado'],
            'accounting_period' => ['Período contable', 'períodos contables', 'apertura contable'],
        ]],
        'finance' => ['caja-bancos.cuentas.index', ['caja y bancos', 'finanzas', 'bancos', 'tesorería'], [
            'budget' => ['Presupuesto'],
            'cash_flow' => ['Flujo de caja'],
            'collection' => ['Cobro bancario'],
            'payment' => ['Pago bancario'],
            'treasury' => ['Tesorería', 'cierre de caja', 'arqueo de caja'],
            'forecast' => ['Proyección financiera'],
            'reconciliation' => ['Conciliación bancaria', 'conciliaciones bancarias', 'conciliación de bancos'],
        ]],
        'payments' => ['clientes.reportes.cuentas-por-cobrar', ['cuentas por cobrar', 'cobros', 'pagos', 'cartera'], [
            'payment' => ['Pago'],
            'collection' => ['Cobro', 'recaudación'],
            'authorization' => ['Autorización del pago'],
            'settlement' => ['Liquidación del pago'],
            'reconciliation' => ['Conciliación de pagos'],
            'refund' => ['Reembolso'],
        ]],
        'tax' => ['tributario.declaraciones.index', ['tributario', 'impuestos', 'tributación'], [
            'tax_invoice' => ['Comprobante tributario'],
            'credit_note' => ['Nota de crédito tributaria'],
            'withholding' => ['Retención', 'retenciones'],
            'tax_report' => ['Reporte tributario', 'ATS', 'anexo transaccional simplificado', 'anexo transaccional'],
            'tax_return' => ['Declaración tributaria', 'declaración de impuestos'],
            'close' => ['Cierre tributario'],
        ]],
        'secretarial' => [null, [], [
            'agenda' => ['Agenda'],
            'meeting' => ['Reunión', 'orden del día'],
            'minutes' => ['Acta de reunión', 'acta'],
            'document' => ['Documento administrativo', 'carta', 'correo administrativo', 'memorando', 'comunicado', 'correo de seguimiento'],
            'reminder' => ['Recordatorio de trabajo', 'recordatorio', 'seguimiento de compromisos', 'seguimiento de pendientes', 'pendientes de hoy', 'pendientes del día', 'mis pendientes', 'tareas de hoy', 'tareas del día', 'tareas diarias', 'mi jornada'],
        ]],
        'fixed_assets' => ['activos-fijos.activos.index', ['activos fijos', 'activo fijo', 'registro de activos'], [
            'asset' => ['Ficha de activo', 'registro de un activo', 'registrar un activo', 'alta de activo'],
            'acquisition' => ['Adquisición de activos', 'adquisiciones'],
            'depreciation' => ['Depreciación de activos', 'depreciación', 'depreciaciones', 'depreciar'],
            'transfer' => ['Traslado de activos', 'traslado de un activo', 'movimientos de activos'],
            'disposal' => ['Baja de activos', 'baja de un activo', 'dar de baja un activo'],
            'maintenance' => ['Mantenimiento de activos', 'orden de mantenimiento', 'órdenes de mantenimiento'],
        ]],
        'hr' => ['rrhh.panel.index', ['recursos humanos', 'rrhh', 'rr hh', 'gestión del personal'], [
            'employee' => ['Ficha de empleado', 'empleados', 'nuevo empleado', 'contrato laboral', 'contratos'],
            'onboarding' => ['Incorporación y salida', 'incorporación', 'ingreso de personal', 'inducción', 'desvinculación'],
            'leave' => ['Vacaciones y permisos', 'vacaciones', 'permiso laboral', 'licencia laboral'],
            'attendance' => ['Asistencia laboral', 'asistencia', 'horas extra'],
            'payroll' => ['Nómina', 'roles de pago', 'décimos'],
            'training' => ['Capacitación', 'capacitar personal', 'certificación de empleados'],
            'recruitment' => ['Reclutamiento', 'vacantes de personal', 'candidatos'],
        ]],
        'administration' => ['administrar-configuracion', ['configuración de Aitrol', 'configuración', 'administración del ERP'], [
            'user' => ['Gestión de usuarios', 'crear un usuario', 'crear usuario', 'crear usuarios', 'alta de usuario', 'crear cuenta de usuario'],
            'role' => ['Roles y permisos', 'rol de acceso', 'roles de acceso', 'roles de usuario', 'rol de usuario', 'permisos de usuario'],
            'company' => ['Configuración de empresa', 'empresas', 'empresa activa', 'cambiar de empresa'],
            'branch' => ['Sucursales', 'sucursal', 'cambiar de sucursal'],
            'settings' => ['Parámetros de la empresa', 'parámetros de empresa', 'preferencias de Aitrol'],
            'period' => ['Contexto operativo', 'seleccionar el período de trabajo', 'período de trabajo', 'período operativo'],
        ]],
    ];

    private const PERMISOS_POR_TEMA = [
        'sales' => ['customer' => 'clientes.lista', 'invoice' => 'ventas.documentos.index'],
        'purchasing' => ['supplier' => 'proveedores.lista', 'purchase_order' => 'compras.ordenes-compra.index', 'supplier_invoice' => 'compras.documentos.index'],
        'inventory' => ['stock' => 'inventarios.existencias.index', 'stock_adjustment' => 'inventarios.ajustes.index', 'transfer' => 'inventarios.movimientos.index', 'stock_count' => 'inventarios.existencias.index'],
        'accounting' => ['ledger' => 'contabilidad.reportes.mayorizacion', 'trial_balance' => 'contabilidad.reportes.balance-comprobacion', 'close' => 'contabilidad.periodos.index', 'accounting_period' => 'contabilidad.periodos.index'],
        'finance' => ['reconciliation' => 'caja-bancos.conciliacion.index'],
        'tax' => ['tax_report' => 'tributario.ats.index'],
        'fixed_assets' => ['acquisition' => 'activos-fijos.adquisiciones.index', 'depreciation' => 'activos-fijos.depreciaciones.index', 'transfer' => 'activos-fijos.movimientos.index', 'disposal' => 'activos-fijos.movimientos.index', 'maintenance' => 'activos-fijos.ordenes.index'],
        'hr' => ['employee' => 'rrhh.empleados.index', 'onboarding' => 'rrhh.incorporacion.index', 'leave' => 'rrhh.permisos.index', 'attendance' => 'rrhh.asistencia.index', 'payroll' => 'rrhh.nomina.index', 'training' => 'rrhh.capacitacion.index', 'recruitment' => 'rrhh.reclutamiento.index'],
        'administration' => ['company' => 'empresas', 'settings' => 'empresas', 'period' => null],
    ];

    private const PERMISOS_POR_ALIAS = [
        'libro diario' => 'contabilidad.reportes.libro-diario',
        'libro de diario' => 'contabilidad.reportes.libro-diario',
        'libro de asientos' => 'contabilidad.reportes.libro-diario',
        'contrato laboral' => 'rrhh.contratos.index',
        'contratos' => 'rrhh.contratos.index',
        'roles de pago' => 'rrhh.roles.index',
        'decimos' => 'rrhh.decimos.index',
        'cierre de caja' => 'caja-bancos.cierres.index',
        'arqueo de caja' => 'caja-bancos.cierres.index',
    ];

    /**
     * @param  array{workflow: string, permiso: ?string, topic?: string}|null  $anterior
     * @return array{workflow: string, permiso: ?string, goal: string, topic?: string, relatedTopic?: string, permisoRelacionado?: ?string}|array{aclaracion: string}|null
     */
    public static function detectar(string $texto, ?array $anterior): ?array
    {
        $frase = trim((string) preg_replace('/[^\p{L}\p{N}]+/u', ' ', Str::lower(Str::ascii($texto))));
        $frase = (string) preg_replace('/\A(?:hola |vicky |por favor |y |(?:me )?(?:puedes|podrias) |me gustaria )+/u', '', $frase);
        if (preg_match('/\A(?:(?:(?:dime|explicame|cual es|que) )?(?:la )?diferencia(?:s)? entre |(?:compara(?:me)?|comparar) )/u', $frase)) {
            return self::comparacion($frase);
        }
        $intenciones = [
            'example' => '/\A(?:(?:(?:dame|muestrame|ponme|hazme|preparame|quiero|necesito|explicame) )?(?:un |una )?(?:ejemplo(?:s)?|caso practico|caso de ejemplo)|como se veria (?:un |una )?)\b/u',
            'checklist' => '/\A(?:(?:(?:dame|muestrame|prepara(?:me)?|haz(?:me)?|quiero|necesito|arma(?:me)?) )?(?:un |una )?(?:checklist|lista de (?:verificacion|control|comprobacion|tareas|pendientes)|rutina diaria)|que (?:debo )?revis(?:ar|o) (?:cada dia|al final del dia|antes de(?:l)?))\b/u',
            'draft' => '/\A(?:crear|crea(?:me)?|preparar|prepara(?:me)?|redactar|redacta(?:me)?|armar|arma(?:me)?|hacer|haz(?:me)?|genera(?:r|me)?|(?:ayudame a )?(?:organizar|planear)|(?:dame|necesito|quiero) (?:un |una )?(?:borrador|modelo|plantilla|agenda|acta|orden del dia))\b/u',
            'troubleshoot' => '/\A(?:que (?:reviso|verifico|hago|compruebo) (?:si|cuando)|por que falla|como (?:resuelvo|corrijo|soluciono)|ayudame a (?:resolver|solucionar|revisar))\b/u',
            'roles' => '/\A(?:quien(?:es)? |que (?:rol|roles|permiso|permisos) |(?:dime|explicame) (?:quien|que permisos))\b/u',
            'controls' => '/\A(?:que (?:controles|validaciones|comprobaciones|riesgos) |como (?:verifico|controlo|valido|compruebo)|(?:dime|explicame) que (?:controles|validaciones))\b/u',
            'requirements' => '/\A(?:que (?:necesito|requisitos|condiciones|documentos|datos) |cuales son los requisitos|que se requiere|(?:dime|explicame) que (?:necesito|requisitos))\b/u',
            'steps' => '/\A(?:como (?:puedo |se )?(?:registr[oa]r?|cre[oa]r?|ingres[oa]r?|hac(?:er|e)|hago|realiz[oa]r?|gener[oa]r?|proces[oa]r?|cerr(?:ar|o)|cierro|concili[oa]r?|ajust[oa]r?|prepar[oa]r?|emit(?:o|e|ir)|recib(?:o|e|ir)|factur[oa]r?|anul[oa]r?|configur[oa]r?|revis[oa]r?|usa(?:r)?|depreci[oa]r?|tramit[oa]r?|solicit[oa]r?|calcul[oa]r?)|cuales son los pasos|que pasos|que tengo que hacer para|(?:guiame|orientame|ayudame|me ayudas) (?:para|en|a|con)|(?:explicame|explicar(?:me)?|dime|decirme|quiero saber|necesito saber|muestrame) como)\b/u',
            'overview' => '/\A(?:como funciona(?:n)?|como es el proceso|como se gestiona|que (?:es|son|significa)|para que sirve|explica(?:r(?:me)?|me)?|dame una guia|cuentame (?:sobre|acerca de)|(?:que puedes decirme|hablame) (?:de|sobre))\b/u',
        ];
        foreach ($intenciones as $objetivo => $patron) {
            if (preg_match($patron, $frase)) {
                $esBorrador = $objetivo === 'draft';
                $ambito = self::ambito($frase, $esBorrador);
                if ($ambito === null && $anterior !== null && preg_match('/\b(?:tiene|necesita|aplica|se requiere|ese proceso|esa guia|este proceso|esta guia|eso|el siguiente paso|dame un ejemplo|dame un caso practico)\b/u', $frase)) {
                    $ambito = $anterior;
                }
                if ($ambito === null && $objetivo === 'checklist' && preg_match('/\b(?:lista de (?:tareas|pendientes)|rutina diaria)\b/u', $frase)) {
                    $ambito = ['workflow' => 'secretarial', 'permiso' => null, 'topic' => 'reminder'];
                }
                if ($ambito === null && $objetivo === 'example') {
                    return ['aclaracion' => '¿De qué proceso de Aitrol necesitas el ejemplo? Puedes decirme, por ejemplo, libro diario, ajuste de inventario u orden de compra.'];
                }
                if ($ambito === null || ($esBorrador && $ambito['workflow'] !== 'secretarial')) {
                    return null;
                }
                if (preg_match('/\b(?:y ademas|tambien dime|tambien explicame|y como)\b/u', $frase)) {
                    return ['aclaracion' => 'Veo más de una pregunta. Indica cuál quieres atender primero para asignarla al especialista adecuado.'];
                }

                return $ambito + ['goal' => $objetivo];
            }
        }

        return null;
    }

    /** @return array{workflow: string, permiso: ?string, goal: string, topic: string, relatedTopic: string, permisoRelacionado: ?string}|array{aclaracion: string}|null */
    private static function comparacion(string $frase): ?array
    {
        $partes = preg_split('/\s+(?:y|con|frente a|vs|versus)\s+/u', $frase);
        if ($partes === false || count($partes) < 2) {
            return null;
        }
        $temas = [];
        foreach ($partes as $parte) {
            $ambito = self::ambito($parte, false);
            if (isset($ambito['topic'])) {
                $temas[$ambito['workflow'].':'.$ambito['topic']] = $ambito;
            }
        }
        if (count($temas) === 2) {
            [$primero, $segundo] = array_values($temas);
            if ($primero['workflow'] === $segundo['workflow']) {
                return $primero + ['goal' => 'compare', 'relatedTopic' => $segundo['topic'], 'permisoRelacionado' => $segundo['permiso']];
            }
        }
        if ($temas === [] || preg_match('/\b(?:hoy|ayer|mes|ano|semana|marzo|abril|enero|febrero|mayo|junio|julio|agosto|septiembre|octubre|noviembre|diciembre|20\d{2})\b/u', $frase)) {
            return null;
        }

        return ['aclaracion' => 'Para comparar procesos, indica dos conceptos del mismo módulo. Por ejemplo, libro diario y libro mayor.'];
    }

    /** @return array<string, string> */
    public static function pasos(string $workflow): array
    {
        return array_map(static fn (array $alias): string => $alias[0], self::MODULOS[$workflow][2] ?? []);
    }

    /** @return array{workflow: string, permiso: ?string, topic?: string}|null */
    private static function ambito(string $frase, bool $borrador): ?array
    {
        $coincidencias = [];
        if (! $borrador && preg_match('/\bcuentas por pagar\b/u', $frase)) {
            $coincidencias['cuentas por pagar'] = ['workflow' => 'payments', 'permiso' => 'proveedores.reportes.cuentas-por-pagar'];
        }
        foreach (self::MODULOS as $flujo => [$permiso, $nombres, $pasos]) {
            if ($borrador && $flujo !== 'secretarial') {
                continue;
            }
            $ambito = ['workflow' => $flujo, 'permiso' => $permiso];
            foreach ($nombres as $nombre) {
                self::coincidencia($frase, $nombre, $ambito, $coincidencias);
            }
            foreach ($pasos as $paso => $nombresPaso) {
                foreach ($nombresPaso as $nombre) {
                    $temas = self::PERMISOS_POR_TEMA[$flujo] ?? [];
                    $acceso = self::PERMISOS_POR_ALIAS[Str::lower(Str::ascii($nombre))] ?? (array_key_exists($paso, $temas) ? $temas[$paso] : $permiso);
                    self::coincidencia($frase, $nombre, ['workflow' => $flujo, 'permiso' => $acceso, 'topic' => $paso], $coincidencias);
                }
            }
        }
        uksort($coincidencias, static fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        return $coincidencias === [] ? null : reset($coincidencias);
    }

    private static function coincidencia(string $frase, string $nombre, array $ambito, array &$coincidencias): void
    {
        $nombre = Str::lower(Str::ascii($nombre));
        if (str_contains(' '.$frase.' ', ' '.$nombre.' ')) {
            $coincidencias[$nombre] = $ambito;
        }
    }
}
