<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Bodegas;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_ventas;
use Sis_medico\De_Empresa;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\contable\VentasController;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Services\V3SalesElectronicDocumentService;
use Sis_medico\Utilidades;
use Sis_medico\v3\contable\Globales;
use Sis_medico\v3\contable\Ventas as V3Venta;

class ReceiptInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function preview(Request $request, $receipt)
    {
        if (!in_array((int) Auth::user()->id_tipo_usuario, array(1, 4, 5, 20, 22), true)) {
            return response()->view('errors.404', array(), 404);
        }

        $companyId = trim((string) $request->session()->get('id_empresa'));
        abort_unless($companyId !== '', 403, 'No se pudo determinar la empresa activa.');

        $order = Ct_Orden_Venta::with(array('detalles.producto', 'pagos.metodo', 'pagos.tarjeta',
            'pagos.ct_banco', 'agenda.paciente', 'seguro', 'empresa'))
            ->where('id_empresa', $companyId)->findOrFail($receipt);
        abort_unless((int) $order->estado === 1, 422, 'Primero debe emitir el recibo.');

        $invoice = Ct_ventas::where('orden_venta', $order->id)->where('id_empresa', $companyId)
            ->where('estado', '<>', 0)->orderByDesc('id')->first();
        $company = $order->empresa ?: Empresa::where('id', $companyId)->where('estado', 1)->firstOrFail();
        $branches = DB::table('ct_sucursales')->where('id_empresa', $companyId)->where('estado', 1)
            ->orderBy('codigo_sucursal')->get();
        $emissionPoints = DB::table('ct_caja as emission_cash')
            ->join('ct_sucursales as emission_branch', 'emission_branch.id', '=', 'emission_cash.id_sucursal')
            ->where('emission_cash.id_empresa', $companyId)->where('emission_cash.estado', 1)
            ->where('emission_branch.id_empresa', $companyId)->where('emission_branch.estado', 1)
            ->select('emission_cash.*')->orderBy('emission_cash.codigo_caja')->get();
        $additionalInformation = $this->receiptAdditionalInformation($order);

        $warnings = array();
        $receiptUrl = route('v3.agenda-receipts.edit', array('id' => $order->id));
        if ($order->detalles->isEmpty()) $warnings[] = array('message' => 'El recibo no tiene productos o servicios.', 'url' => $receiptUrl, 'label' => 'Revisar recibo');
        if ($order->pagos->isEmpty()) $warnings[] = array('message' => 'El recibo no tiene una forma de pago registrada.', 'url' => $receiptUrl, 'label' => 'Agregar forma de pago');
        if ((float) $order->total <= 0) $warnings[] = array('message' => 'El total del recibo debe ser mayor que cero.', 'url' => $receiptUrl, 'label' => 'Revisar valores');
        if (trim((string) $order->identificacion) === '' || trim((string) $order->razon_social) === '') {
            $warnings[] = array('message' => 'Faltan la identificación o la razón social del cliente.', 'url' => $receiptUrl, 'label' => 'Completar cliente');
        }
        if ($branches->isEmpty()) $warnings[] = array('message' => 'La empresa no tiene un establecimiento activo para facturar.', 'url' => route('establecimiento.index'), 'label' => 'Crear establecimiento');
        if ($emissionPoints->isEmpty()) $warnings[] = array('message' => 'La empresa no tiene un punto de emisión activo para facturar.', 'url' => route('punto_emision.index'), 'label' => 'Crear punto de emisión');

        $view = 'v3.contable.receipt_invoice.preview';
        $payload = compact('order', 'invoice', 'company', 'branches', 'emissionPoints', 'warnings', 'additionalInformation');
        config(array('data' => array_merge(config('data') ?: array(), array(
            'controlador' => 'agenda-clinica', 'carpeta' => 'contable', 'submodulo' => 'facturar-recibo',
            'submodulo_' => 'facturar-recibo', 'titulo' => 'Vista previa de factura',
            'title' => 'Vista previa de factura | Aitrol Hospitalario', 'vista' => $view,
            'contenido' => view($view, $payload),
        ))));
        return response()->view('v3.layouts.view_child')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Emite directamente la factura con la misma rutina contable, de cartera,
     * pagos y facturación electrónica que utiliza el módulo de ventas.
     */
    public function storeInvoice(Request $request, $receipt)
    {
        if (!in_array((int) Auth::user()->id_tipo_usuario, array(1, 4, 5, 20, 22), true)) {
            return response()->view('errors.404', array(), 404);
        }

        $companyId = trim((string) $request->session()->get('id_empresa'));
        abort_unless($companyId !== '', 403, 'No se pudo determinar la empresa activa.');

        try {
            $order = Ct_Orden_Venta::with(array('detalles.producto', 'pagos', 'agenda.paciente', 'empresa'))
                ->where('id_empresa', $companyId)->where('estado', 1)->findOrFail($receipt);

            $existingInvoice = Ct_ventas::where('orden_venta', $order->id)->where('id_empresa', $companyId)
                ->where('estado', '<>', 0)->orderByDesc('id')->first();
            if ($existingInvoice) {
                $workflow = $this->ensureElectronicWorkflow($existingInvoice, $companyId);
                return $this->invoiceRedirect($order->id, 'success', $this->successMessage($existingInvoice, $workflow));
            }

            if ($order->detalles->isEmpty()) {
                throw new \RuntimeException('El recibo no tiene productos o servicios para facturar.');
            }
            if ($order->pagos->isEmpty()) {
                throw new \RuntimeException('El recibo no tiene una forma de pago registrada.');
            }
            if ((float) $order->total <= 0) {
                throw new \RuntimeException('El total del recibo debe ser mayor que cero.');
            }
            if (!$order->agenda || !$order->agenda->paciente) {
                throw new \RuntimeException('El recibo no tiene una agenda y paciente válidos.');
            }

            $company = $order->empresa ?: Empresa::where('id', $companyId)->where('estado', 1)->firstOrFail();
            list($branch, $emissionPoint) = $this->resolveEmissionPoint($companyId, $company);
            $payload = $this->invoicePayload($order, $company, $branch, $emissionPoint, $companyId);

            // Conserva la sesión, el usuario y el contexto de la petición
            // original; solo sustituye los campos que normalmente enviaba el
            // formulario manual de facturación.
            $invoiceRequest = clone $request;
            $invoiceRequest->replace($payload);
            $result = app(VentasController::class)->store($invoiceRequest);

            $invoice = Ct_ventas::where('orden_venta', $order->id)->where('id_empresa', $companyId)
                ->where('estado', '<>', 0)->orderByDesc('id')->first();
            if (is_array($result) && ($result['status'] ?? null) === 'success' && $invoice) {
                $workflow = $this->ensureElectronicWorkflow($invoice, $companyId);
                return $this->invoiceRedirect($order->id, 'success', $this->successMessage($invoice, $workflow));
            }
            // También cubre dos solicitudes simultáneas: si la otra ya
            // creó la factura, se informa como resultado correcto.
            if ($invoice) {
                $workflow = $this->ensureElectronicWorkflow($invoice, $companyId);
                return $this->invoiceRedirect($order->id, 'success', $this->successMessage($invoice, $workflow));
            }

            // Los procesos contables devuelven un mensaje general en `msj`
            // y el motivo corregible en `exp` o `error`. Mostrar primero el
            // motivo evita ocultar configuraciones faltantes detrás de un
            // simple "No se creo el comprobante".
            $message = 'El proceso de facturación no devolvió una respuesta válida.';
            if (is_array($result)) {
                $message = 'No fue posible crear la factura.';
                foreach (array('exp', 'error', 'msj', 'mensaje') as $field) {
                    if (!isset($result[$field]) || trim(strip_tags((string) $result[$field])) === '') continue;
                    $message = $result[$field];
                    break;
                }
            }
            throw new \RuntimeException(strip_tags((string) $message));
        } catch (\Throwable $exception) {
            report($exception);
            return $this->invoiceRedirect(
                $receipt,
                'error',
                $exception->getMessage(),
                $this->errorAction($exception->getMessage(), $receipt, $companyId)
            );
        }
    }

    private function resolveEmissionPoint($companyId, $company)
    {
        $branches = Ct_Sucursales::where('id_empresa', $companyId)->where('estado', 1)
            ->orderBy('codigo_sucursal')->get();
        if ($branches->isEmpty()) {
            throw new \RuntimeException('La empresa no tiene un establecimiento activo para facturar.');
        }

        $configuredBranch = trim((string) $company->establecimiento);
        $branch = $branches->first(function ($item) use ($configuredBranch) {
            return $configuredBranch !== '' && (string) $item->codigo_sucursal === $configuredBranch;
        }) ?: $branches->first();

        $points = Ct_Caja::where('id_empresa', $companyId)->where('id_sucursal', $branch->id)
            ->where('estado', 1)->orderBy('codigo_caja')->get();
        if ($points->isEmpty()) {
            $point = Ct_Caja::where('ct_caja.id_empresa', $companyId)->where('ct_caja.estado', 1)
                ->join('ct_sucursales', 'ct_sucursales.id', '=', 'ct_caja.id_sucursal')
                ->where('ct_sucursales.id_empresa', $companyId)->where('ct_sucursales.estado', 1)
                ->select('ct_caja.*')->orderBy('ct_sucursales.codigo_sucursal')->orderBy('ct_caja.codigo_caja')->first();
            if (!$point) {
                throw new \RuntimeException('La empresa no tiene un punto de emisión activo para facturar.');
            }
            $branch = $branches->firstWhere('id', $point->id_sucursal);
            return array($branch, $point);
        }

        $configuredPoint = trim((string) $company->punto_emision);
        $point = $points->first(function ($item) use ($configuredPoint) {
            return $configuredPoint !== '' && (string) $item->codigo_caja === $configuredPoint;
        }) ?: $points->first();

        return array($branch, $point);
    }

    private function invoicePayload($order, $company, $branch, $emissionPoint, $companyId)
    {
        $taxConfiguration = Utilidades::porcentajeIva($companyId);
        if (!$taxConfiguration) {
            throw new \RuntimeException('La empresa no tiene configurado el porcentaje de IVA.');
        }

        $defaultWarehouse = Ct_Bodegas::where('id_empresa', $companyId)->where('estado', 1)->orderBy('id')->first();
        $names = $quantities = $warehouses = $codes = $prices = $discountRates = array();
        $insuranceAmounts = $discounts = $netPrices = $descriptions = $taxes = $inventorySeries = array();

        foreach ($order->detalles as $detail) {
            $product = $detail->producto;
            if (!$product || (string) $product->id_empresa !== (string) $companyId || (int) $product->estado_tabla !== 1) {
                $product = Ct_productos::where('id_empresa', $companyId)->where('estado_tabla', 1)
                    ->where(function ($query) use ($detail) {
                        $query->where('id', $detail->id_producto)->orWhere('codigo', $detail->cod_prod);
                    })->first();
            }
            if (!$product) {
                throw new \RuntimeException('El producto del detalle "' . ($detail->nombre_producto ?: $detail->descripcion) . '" no está activo en esta empresa.');
            }

            $names[] = $product->nombre;
            $quantities[] = max(0.0001, (float) $detail->cantidad);
            $warehouses[] = $detail->bodega ?: ($defaultWarehouse ? $defaultWarehouse->id : '');
            $codes[] = $product->codigo;
            $prices[] = (float) $detail->precio;
            $discountRates[] = (float) $detail->p_dcto;
            $insuranceAmounts[] = (float) $detail->valor_oda;
            $discounts[] = (float) $detail->descuento;
            $netPrices[] = (float) $detail->total;
            $descriptions[] = $detail->descripcion ?: $detail->nombre_producto ?: $product->nombre;
            $taxes[] = (int) $product->iva === 1 ? 1 : 0;
            $inventorySeries[] = $detail->id_inv_serie ?: '';
        }

        $patient = $order->agenda->paciente;
        $patientName = trim(implode(' ', array_filter(array(
            $patient->nombre1, $patient->nombre2, $patient->apellido1, $patient->apellido2,
        ))));
        $user = Auth::user();
        $userName = trim(implode(' ', array_filter(array(
            $user->nombre1, $user->nombre2, $user->apellido1, $user->apellido2,
        ))));
        $currency = Ct_Divisas::where('estado', 1)->orderBy('id')->first();
        if (!$currency) {
            throw new \RuntimeException('No existe una divisa activa para generar la factura.');
        }

        $additionalInformation = $this->receiptAdditionalInformation($order);

        $payload = array(
            'id' => '', 'numero' => null, 'tipo' => 'VEN-FA', 'asiento' => '',
            'fecha_asiento' => date('Y-m-d'), 'orden_venta' => $order->id,
            'v3_receipt_invoice' => 1,
            'electronica' => ((int) $company->electronica === 1 || De_Empresa::getDatos($companyId)) ? 1 : 0,
            'diasPlazo' => 30, 'sucursal' => $branch->id, 'punto_emision' => $emissionPoint->id,
            'numero_autorizacion' => '', 'divisas' => $currency->id,
            'recaudador' => $user->id, 'cedula_recaudador' => $user->id,
            'vendedor' => $userName, 'cedula_vendedor' => $user->id,
            'identificacion_cliente' => $order->identificacion,
            'nombre_cliente' => $order->razon_social,
            'cliente_nombre' => $order->razon_social,
            'direccion_cliente' => $order->direccion ?: 'S/D',
            'ciudad_cliente' => $order->ciudad ?: 'S/D',
            'mail_cliente' => $order->email ?: '', 'telefono_cliente' => $order->telefono ?: '',
            'identificacion_paciente' => $patient->id, 'nombre_paciente' => $patientName,
            'id_seguro' => $order->id_seguro, 'id_nivel' => $order->id_nivel,
            'procedimiento' => substr(implode(', ', $names), 0, 250),
            'fecha_proced' => date('Y-m-d', strtotime($order->agenda->fechaini)),
            'tipo_consulta' => (int) $order->agenda->proc_consul === 0 ? 1 : 2,
            'mov_paciente' => '', 'concepto' => $order->observacion ?: 'Factura del recibo #' . $order->id,
            'nota' => $order->observacion ?: 'Factura del recibo #' . $order->id,
            'nota_electronica' => array(), 'nota_array' => $additionalInformation,
            'kardex_descripcion' => 'Factura del recibo #' . $order->id,
            'contraentrega' => 0, 'id_venta' => '', 'proforma' => '',
            'nombre' => $names, 'cantidad' => $quantities, 'bodega' => $warehouses,
            'codigo' => $codes, 'precio' => $prices, 'descpor' => $discountRates,
            'copago' => $insuranceAmounts, 'desc' => $discounts, 'precioneto' => $netPrices,
            'descrip_prod' => $descriptions, 'iva' => $taxes, 'id_inv_serie' => $inventorySeries,
            'ivareal' => (float) $taxConfiguration->iva,
            'subtotal_121' => (float) $order->subtotal_12,
            'subtotal_01' => (float) $order->subtotal_0,
            'subtotal1' => (float) $order->subtotal_12 + (float) $order->subtotal_0,
            'descuento1' => (float) $order->descuento,
            'tarifa_iva1' => (float) $order->iva,
            'total1' => (float) $order->total,
            'totalc' => array_sum($insuranceAmounts), 'transporte' => 0,
            'contador_pago' => $order->pagos->count(),
            'valor_totalPagos' => (float) $order->pagos->sum('valor'),
        );

        foreach ($order->pagos->values() as $index => $payment) {
            $payload['id_tip_pago' . $index] = $payment->tipo;
            $payload['visibilidad_pago' . $index] = 1;
            $payload['fecha_pago' . $index] = $payment->fecha ?: date('Y-m-d');
            $payload['tipo_tarjeta' . $index] = $payment->tipo_tarjeta ?: '';
            $payload['numero_pago' . $index] = $payment->numero ?: '';
            $payload['id_banco_pago' . $index] = $payment->banco ?: '';
            $payload['id_cuenta_pago' . $index] = $payment->cuenta ?: '';
            $payload['giradoa' . $index] = $payment->giradoa ?: '';
            $payload['valor' . $index] = (float) $payment->valor;
            $payload['valor_base' . $index] = (float) $payment->valor;
        }

        return $payload;
    }

    /**
     * Construye la información adicional de la factura directamente desde
     * la copia editable guardada en ct_orden_venta.diagnosticos_consulta.
     */
    private function receiptAdditionalInformation($order)
    {
        $diagnoses = trim((string) $order->diagnosticos_consulta);
        if ($diagnoses === '') return array();

        return array(array(
            'nombre' => 'Diagnósticos',
            'valor' => $diagnoses,
        ));
    }

    private function ensureElectronicWorkflow($invoice, $companyId)
    {
        if (!(int) $invoice->electronica) return null;

        $sale = V3Venta::where('id_empresa', $companyId)->where('id', $invoice->id)
            ->where('estado', '<>', 0)->firstOrFail();
        $document = app(V3SalesElectronicDocumentService::class)->register($sale);
        if (!$document) return null;

        $workflow = $document->sriWorkflow()->first();
        if ($workflow && $workflow->status === 'failed') {
            throw new \RuntimeException(
                'La factura quedó guardada, pero falló su proceso electrónico: '
                . ($workflow->last_error ?: 'revise el diagnóstico del proceso SRI.')
            );
        }

        return $workflow;
    }

    private function successMessage($invoice, $workflow = null)
    {
        $number = trim((string) $invoice->nro_comprobante);
        $message = 'Recibo facturado correctamente' . ($number !== '' ? '. Factura ' . $number . '.' : '.');
        if (!$workflow) return $message;
        if ($workflow->status === 'emailed') return $message . ' Autorizada por el SRI y enviada por correo.';
        if ($workflow->status === 'authorized') return $message . ' Autorizada por el SRI.';
        if ($workflow->status === 'waiting_authorization') {
            return $message . ' XML generado, firmado y recibido por el SRI; autorización pendiente.';
        }
        return $message . ' Proceso electrónico iniciado.';
    }

    /**
     * Enlaza cada error corregible con la pantalla donde debe resolverse.
     */
    private function errorAction($message, $receipt, $companyId)
    {
        $message = (string) $message;
        if (stripos($message, 'CONFIG_CONTABLE') !== false
            && stripos($message, 'cuenta base de') !== false) {
            return array(
                'url' => route('v3.global-settings.index') . '#cash-bank-settings',
                'label' => 'Configurar cuenta base de Caja/Bancos',
            );
        }
        if (stripos($message, 'CONFIG_CONTABLE') !== false
            && (stripos($message, 'caja o cuenta bancaria') !== false
                || stripos($message, 'caja operativa') !== false
                || stripos($message, 'cuenta para') !== false
                || stripos($message, 'forma de pago') !== false)) {
            return array(
                'url' => url('v3/cajabancos/crearcajabanco'),
                'label' => 'Crear o vincular la caja operativa',
            );
        }
        if (stripos($message, 'CONFIG_CONTABLE') !== false
            && stripos($message, 'no pertenece al plan') !== false) {
            return array(
                'url' => route('plan_cuentas.index'),
                'label' => 'Revisar el plan de cuentas de la empresa',
            );
        }
        if (stripos($message, 'CONFIG_CONTABLE') !== false) {
            $settings = array(
                'tarifa 0' => array(Globales::VENTA_INGRESOS_0, 'Configurar ingresos tarifa 0 %'),
                'ingresos gravados' => array(Globales::VENTA_INGRESOS_GRAVADOS, 'Configurar ingresos gravados'),
                'iva generado' => array(Globales::VENTA_IVA, 'Configurar IVA generado'),
                'cuenta por cobrar' => array(Globales::VENTA_CUENTAS_COBRAR, 'Configurar cuenta por cobrar'),
                'descuentos' => array(Globales::VENTA_DESCUENTOS, 'Configurar descuentos en ventas'),
            );
            $anchor = 'account-settings';
            $label = 'Abrir matriz contable de Ventas';
            foreach ($settings as $needle => $setting) {
                if (stripos($message, $needle) === false) continue;
                $anchor = 'global-setting-' . \Illuminate\Support\Str::slug($setting[0]);
                $label = $setting[1];
                break;
            }
            return array('url' => route('v3.global-settings.index') . '#' . $anchor, 'label' => $label);
        }

        if (stripos($message, 'establecimiento activo') !== false) {
            return array('url' => route('establecimiento.index'), 'label' => 'Administrar establecimientos');
        }
        if (stripos($message, 'punto de emisi') !== false) {
            return array('url' => route('punto_emision.index'), 'label' => 'Administrar puntos de emisión');
        }
        if (stripos($message, 'producto') !== false) {
            return array('url' => route('v3.product-category-accounts.index'), 'label' => 'Configurar cuentas de productos');
        }
        if (stripos($message, 'SRI') !== false || stripos($message, 'electrónic') !== false
            || stripos($message, 'XML') !== false || stripos($message, 'firma') !== false
            || stripos($message, 'clave de acceso') !== false) {
            return array('url' => route('sri.workflows.index'), 'label' => 'Revisar procesos electrónicos SRI');
        }
        if (stripos($message, 'recibo') !== false || stripos($message, 'forma de pago') !== false
            || stripos($message, 'agenda y paciente') !== false) {
            return array('url' => route('v3.agenda-receipts.edit', array('id' => $receipt)), 'label' => 'Revisar el recibo');
        }

        return null;
    }

    private function invoiceRedirect($receipt, $type, $message, $action = null)
    {
        $response = redirect()->route('v3.agenda-receipts.invoice-preview', array('receipt' => $receipt))
            ->with($type === 'success' ? 'invoice_success' : 'invoice_error', $message);
        if ($action && !empty($action['url'])) {
            $response->with('invoice_action_url', $action['url'])
                ->with('invoice_action_label', $action['label'] ?? 'Resolver ahora');
        }
        return $response;
    }
}
