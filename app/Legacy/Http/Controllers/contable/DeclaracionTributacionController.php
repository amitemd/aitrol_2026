<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\Declare_;
use Sis_medico\Ct_Cliente_Retencion;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Credito_Acreedores;
use Sis_medico\Ct_detalle_compra;
use Sis_medico\Ct_detalle_retenciones;
use Sis_medico\Ct_detalle_venta;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Ct_Nota_Credito_Clientes;
use Sis_medico\Ct_Porcentaje_Retenciones;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_ventas;
use Sis_medico\De_Codigo_Impuestos;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\excelCreate;

class DeclaracionTributacionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view("contable/declaracion_tributacion/index");
    }

    public static function busqueda(Request $request)
    {

        $data = [];
        $table = "";
        switch ($request->tipo) {
            case "V":
                $title = ["Fecha", "Número", "Autorización", "CI", "Cliente", "Seguro", "Tipo", "Detalle", "SubTotal12", "SubTotal0", "Subtotal", "Descuento", "Impuesto", "Total", "Estado", "Creado por", "Anulado por"];
                $data = DeclaracionTributacionController::sqlQueryVenta($request->all());
                $table = DeclaracionTributacionController::tableVentas($title, $data);
                return $table;
                break;

            case "T":
                /* Para ventas */
                $titles = [];
                /********************TITULO PARA VENTAS  **********************/
                $titles["ventas"] = ["CLASIFICACION", "SISTEMA", "FECHA", "CLIENTE", "RUC", "AUT. SRI", "OBSERVACION", "DOC.", "Nº FACTURA", "BASE 0%", "BASE 12%", "SUBTOTAL", "IVA", "TOTAL",/* "0,0100","0,03", "0,08", "0,70", "1,00", "TOTAL A RECIBIR", "# RETENCION", "AUTOR #"*/];
                /************************************************************ */

                /**********************TITULOS PARA COMPRAS*****************************/
                $titles["compras"] = ["", "N. Comp. SISTEMA", "Fecha", "PROVEEDOR", "RUC", "AUT. SRI", "OBSERVACION", "DOC.", "# FACTURA", "Tarifa 0%", "Base Impo12%", "SUBTOTAL", "0,12", "TOTAL FACTURA"];
                /**********************************************************************/

                /*************************Consultas a las tablas**************************/
                $dataVentas = DeclaracionTributacionController::sqlQueryVenta($request->all());
                $dataCompras = DeclaracionTributacionController::sqlQueryCompra($request->all());
                $dataNCliente = DeclaracionTributacionController::sqlNCreditoCliente($request->all());
                $dataNCAcreedor = DeclaracionTributacionController::sqlNCreditoAcreedor($request->all());

                $data["ventas"] = $dataVentas;
                $data["compras"] = $dataCompras;
                $data["nCliente"] = $dataNCliente;
                $data["nCAcreedor"] = $dataNCAcreedor;
                /*************************************************************************/
                $tableTodo = DeclaracionTributacionController::tableTodo($titles, $data, $request->all());
                break;

            case "C":
                $titles = ["Fecha", "Número", "Ruc", "Proveedor", "Tipo Comprobante", "Tipo", "Detalle", "SubTotal12", "SubTotal0", "SubTotal", "Descuento", "Impuesto", "Total", "Estado", "Creado Por", "Anulado Po"];
                $data = DeclaracionTributacionController::sqlQueryCompra($request->all());
                $table = DeclaracionTributacionController::tableCompras($titles, $data);
                return $table;
                break;

            case "RA":
                // $titlesRetAc=["Fecha", "Número", "Preimpresa", "Acreedor", "Ruc", "Detalle", "Total RFIR.", "%", "Total RFIVA", "%", "Estado", "Creado por","Anulado por"];
                $titlesRetAc = [
                    "COD COMPR",
                    "#COMP",
                    "# RETENCION",
                    "AUTORIZACION",
                    "FECHA",
                    "PROVEEDOR",
                    "RUC",
                    "SERIE",
                    "FACTURA",
                    "TARIFA 12%",
                    "TARIFA 0%",
                    "SUBTOTAL",
                    "IVA",
                    "TOTAL",
                    "% RET FTE",
                    "VALOR RETENIDO",
                    "CODIGO",
                    "% RET IVA",
                    "VALOR RETENIDO",
                    "CODIGO",
                    "CADUCIDAD"
                ];
                $data   = DeclaracionTributacionController::sqlRetCompras($request->all());
                $table  = DeclaracionTributacionController::tableRetCompras($titlesRetAc, $data);
                return $table;
                break;

            case 'RC':
                $titlesRetVentas = ["Fecha", "Número", "Preimpresa", "Cliente", "RUC", "Detalle", "Total RFIR.", "%", "Total RFIVA", "%", "Estado", "Creado por", "Anulado por"];
                $data   = DeclaracionTributacionController::sqlRetventas($request->all());
                $table  = DeclaracionTributacionController::tableRetVentas($titlesRetVentas, $data);
                return $table;
                break;

            case "C":
                //Para compras
                $titleCompra = ["FECHA", "NUMERO", "RUC", "PROVEEDOR", "TIPO COMPROBANTE", "TIPO", "DETALLE", "SUBTOTAL12", "SUBTOTAL0", "SUBTOTAL", "SUBTOTAL12 CON DESCUENTO", "SUBTOTAL0 CON DESCUENTO", "DESCUENTO", "IMPUESTO", "TOTAL", "ESTADO", "CREADO POR", "ANULADO POR"];
                $data = DeclaracionTributacionController::slqQueryCompra($request);
                $table = DeclaracionTributacionController::tableCompras($titleCompra, $data);
                return $table;
                break;
        }


        return ["status" => "error", "table" => $table, "msj" => "No hay resultados"];
    }

    public static function slqQueryCompra(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $estado = $request['estado'];


        $compras = Ct_compras::where('id_empresa', $id_empresa);

        if (!is_null($estado) || $estado != "") {
            if ($estado == 0) {
                $compras = $compras->where('estado', $estado);
            } elseif ($estado == 1) {
                $compras = $compras->where('estado', '>', '0');
            }
        }

        if (!is_null($fecha_desde)) {
            $compras = $compras->wherebetween('f_autorizacion', [str_replace('/', '-', $fecha_desde), str_replace('/', '-', $fecha_hasta)])->orderBy('f_autorizacion', 'ASC');
        } else {
            $compras = $compras->where('f_autorizacion', '<=', str_replace('/', '-', $fecha_hasta))->orderBy('f_autorizacion', 'ASC');
        }

        $totales = 0;
        $subtotal12 = 0;
        $subtotal0 = 0;
        $subtotal = 0;
        $descuento = 0;
        $impuesto = 0;
        $arrayDatos = [];
        foreach ($compras->get() as $compra) {

            $totales += $compra->total_final;
            $subtotal12 += $compra->subtotal_12;
            $subtotal += $compra->subtotal;
            $subtotal0  += $compra->subtotal_0;
            $descuento += $compra->descuento;
            $impuesto += $compra->iva_total;

            //
            $subtotal12Descuento =  $compra->detalles->where('iva', 1)->sum('descuento');
            $subtotal0Descuento = $compra->detalles->where('iva', 0)->sum('descuento');
            if ($subtotal12Descuento > 0) {
                $subtotal12Descuento = $compra->subtotal_12 - $subtotal12Descuento;
            } else {
                $subtotal12Descuento = $compra->subtotal_12;
            }

            if ($subtotal0Descuento > 0) {
                $subtotal0Descuento = $compra->subtotal_0 - $subtotal0Descuento;
            } else {
                $subtotal0Descuento = $compra->subtotal_0;
            }



            array_push($arrayDatos, [
                'fecha_autorizacion' => date("d-m-Y", strtotime($compra->f_autorizacion)),
                'numero' => $compra->numero,
                'ruc' => $compra->proveedor,
                'proveedor' => $compra->proveedorf != null ? $compra->proveedorf->nombrecomercial : '',
                'tipo_comprobante' => $compra->master_tipo == null ? ''  : $compra->master_tipos->nombre,
                'tipo' => $compra->tipo == 1 ? 'COM-FA' : 'COM-FACT',
                'detalle' => $compra->observacion . ' ' . ' Asiento #' . $compra->id_asiento_cabecera,
                'subtotal12' => $compra->subtotal_12,
                'subtotal0' => $compra->subtotal_0,
                'subtotal' => $compra->subtotal,
                'subtotal12_descuento' => $subtotal12Descuento,
                'subtotal0_descuento' => $subtotal0Descuento,
                'descuento' => $compra->descuento > 0 ? $compra->descuento : 0.00,
                'impuesto' => $compra->iva_total > 0 ? $compra->iva_total : 0.0,
                'total' => $compra->total_final,
                'estado' => $compra->estado == 0 ? 'INACTIVA' : 'ACTIVA',
                'creado' => $compra->usuario->nombre1 . ' ' . $compra->usuario->apellido1,
                'anulado_por' => $compra->estado == 0 ? $compra->usuario->nombre1 . ' ' . $compra->usuario->apellido1  : '',
                'id_asiento' => $compra->id_asiento_cabecera,
                'id' => $compra->id
            ]);
        }

        return [$arrayDatos, $totales, $subtotal12, $subtotal0, $subtotal, $descuento, $impuesto];
    }


    public static function sqlQueryVenta($data)
    {
        $ventas = Ct_ventas::where("id_empresa", Session::get("id_empresa"));


        if (!is_null($data["fecha_desde"])) {
            $ventas = $ventas->where('fecha',  '>=', $data["fecha_desde"]);
        }

        if (!is_null($data["fecha_hasta"])) {
            $ventas = $ventas->where('fecha', '<=', $data["fecha_hasta"]);
        }
        // dd($data["estado"]);

        if ($data["estado"] == "T") {
            $ventas = $ventas->where('estado', '<>', NULL);
        } else if ($data["estado"] == 1) {
            $ventas = $ventas->where('estado', ">=", 1);
        } else if ($data["estado"] == 0) {
            $ventas = $ventas->where('estado', 0);
        }

        $arrVentas = [];
        foreach ($ventas->get() as $venta) {
            $subtotal0 = 0;
            $subtotal12 = 0;
            $descuento = 0;
            $impuesto = 0;

            foreach ($venta->detalles as $detalle) {
                if ($detalle->check_iva == 1) {
                    $subtotal12 += $detalle->extendido;
                    $impuesto += ($detalle->extendido * $detalle->porcentaje);
                } else {
                    $subtotal0 += $detalle->extendido;
                }

                $descuento += $detalle->descuento;
            }
            // dd($venta);

            $cliente = isset($venta->cliente) ? $venta->cliente->nombre : $venta->id_ciente;
            $seguro = isset($venta->seguro) ? $venta->seguro->nombre : $venta->id_seguro;
            $creado = isset($venta->usuario) ? $venta->usuario->nombre1 . " " . $venta->usuario->apellido1 : $venta->id_usuariocrea;
            $anulado = "";
            $estado = "ACTIVA";

            if ($venta->estado == 0) {
                $anulado = isset($venta->usuariomod) ? $venta->usuariomod->nombre1 . " " . $venta->usuariomod->apellido1 : $venta->id_usuariomod;
                $estado = "ANULADA";
            }

            array_push($arrVentas, [
                "id" => $venta->id,
                "fecha" => $venta->fecha,
                "comprobante" => $venta->nro_comprobante,
                "nro_autorizacion" => $venta->nro_autorizacion,
                "id_cliente" => $venta->id_cliente,
                "cliente" => $cliente,
                "seguro" => $seguro,
                "tipo" => $venta->tipo,
                "id_asiento" => $venta->id_asiento,
                "detalle" => $venta->concepto,
                "subtotal12" => $subtotal12,
                "subtotal0" => $subtotal0,
                "descuento" => $descuento,
                "impuesto" => $impuesto,
                "estado" => $estado,
                "creado" => $creado,
                "anulado" => $anulado
            ]);
        }
        return $arrVentas;
    }

    public static function sqlQueryCompra($data)
    {
        $compras = Ct_compras::where("id_empresa", Session::get('id_empresa'))->where('archivo_sri', 1);
        /*if (Auth::user()->id == '0921605895') {
            echo '<pre>';
            print_r($compras->get());
            exit;
        }*/
        if (!is_null($data["fecha_desde"])) {
            $compras = $compras->where('f_autorizacion',  '>=', $data["fecha_desde"]);
        }
        if (!is_null($data["fecha_hasta"])) {
            $compras = $compras->where('f_autorizacion', '<=', $data["fecha_hasta"]);
        }
        if ($data["estado"] == "T") {
            $compras = $compras->where('estado', '<>', NULL);
        } else if ($data["estado"] == 1) {
            $compras = $compras->where('estado', ">=", 1);
        } else if ($data["estado"] == 0) {
            $compras = $compras->where('estado', 0);
        }
        $arrCompras = [];
        foreach ($compras->get() as $compra) {
            $subtotal0 = 0;
            $subtotal12 = 0;
            $descuento = 0;
            $impuesto = 0;
            $subtotal12 += $compra->subtotal_12;
            $impuesto += $compra->iva_total;
            $subtotal0 += $compra->subtotal_0;
            $descuento += $compra->descuento;
            $subtotal = $compra->subtotal;
            // foreach($compra->detalles as $detalle){
            //     if ($detalle->iva == 1) {
            //         $subtotal12 += ($detalle->precio * $detalle->cantidad) -  $detalle->descuento;
            //         $impuesto += (($detalle->precio * $detalle->cantidad) -  $detalle->descuento) *  $detalle->porcentaje;
            //         // $impuesto += ($detalle->extendido * $detalle->porcentaje);
            //     } else {
            //         $subtotal0 += ($detalle->precio * $detalle->cantidad) -  $detalle->descuento;

            //         // $subtotal0 += $detalle->extendido;
            //     }

            //     $descuento += $detalle->descuento;
            // }
            $total = $compra->total_final;
            $creado = isset($compra->usuario) ? $compra->usuario->nombre1 . " " . $compra->usuario->apellido1 : $compra->id_usuariocrea;
            $anulado = "";
            $estado = "ACTIVA";
            if ($compra->estado == 0) {
                $anulado = isset($compra->usuariomod) ? $compra->usuariomod->nombre1 . " " . $compra->usuariomod->apellido1 : $compra->id_usuariomod;
                $estado = "ANULADA";
            }
            $proveedor = isset($compra->proveedorf) ? $compra->proveedorf->razonsocial : $compra->proveedor;
            $tipo = "";
            $activo = explode(" ", trim($compra->observacion));
            if ($activo[0] == "ACT-FA") {
                $tipo = "COM-AF";
            } else if ($compra->tipo == 1) {
                $tipo = 'COM-FA';
            } else {
                $tipo = 'COM-FACT';
            }
            $cons_tipo_comprobante = Ct_master_tipos::where("codigo", $compra->tipo_comprobante)->first();
            $tipo_comprobante = !is_null($cons_tipo_comprobante) ? $cons_tipo_comprobante->nombre : "";
            array_push($arrCompras, [
                "id" => $compra->id,
                "fecha" => $compra->f_autorizacion,
                "comprobante" => $compra->numero,
                "nro_autorizacion" => $compra->autorizacion,
                "id_proveedor" => $compra->proveedor,
                "proveedor" => $proveedor,
                "tipo" => $tipo,
                "tipo_comprobante" => $tipo_comprobante,
                "id_asiento" => $compra->id_asiento_cabecera,
                "detalle" => $compra->observacion,
                "subtotal12" => $subtotal12,
                "subtotal0" => $subtotal0,
                "subtotal" => $subtotal,
                "descuento" => $descuento,
                "impuesto" => $impuesto,
                "total" => $total,
                "estado" => $estado,
                "creado" => $creado,
                "anulado" => $anulado
            ]);
        }
        return $arrCompras;
    }

    public static function sqlNCreditoCliente($data)
    {
        $ncreditos = Ct_Nota_Credito_Clientes::where('id_empresa', Session::get('id_empresa'));
        $arrNCredito = [];

        if ($data["estado"] == "T") {
            $ncreditos = $ncreditos->where('estado', '<>', NULL);
        } else if ($data["estado"] == 1) {
            $ncreditos = $ncreditos->where('estado', ">=", 1);
        } else if ($data["estado"] == 0) {
            $ncreditos = $ncreditos->where('estado', 0);
        }



        if (isset($data["id_venta"])) {
            $ncreditos = $ncreditos::where("id_factura", $data["id_venta"])->first();
            $arrNCredito["id_cliente"]      = $ncreditos->id_cliente;
            $arrNCredito["cliente"]         = isset($ncreditos->cliente) ? $ncreditos->nombre : $ncreditos->id_cliente;
            $arrNCredito["tipo"]            = $ncreditos->tipo;
            $arrNCredito["id_asiento"]      = $ncreditos->id_asiento_cabecera;
            $arrNCredito["id_asiento_url"]  = url("contable/cliente/nota_credito/editar2") . "/" . $ncreditos->id_asiento_cabecera;
            $arrNCredito["nro_comprobante"] = $ncreditos->nro_comprobante;
            $arrNCredito["fecha"]           = $ncreditos->fecha;
            $arrNCredito["concepto"]      = $ncreditos->concepto;
            $arrNCredito["subtotal0"]       = $ncreditos->subtotal0;
            $arrNCredito["subtotal12"]      = $ncreditos->subtotal12;
            $arrNCredito["subtotal"]        = ($ncreditos->subtotal12 + $ncreditos->subtotal0);
            $arrNCredito["impuesto"]        = $ncreditos->impuesto;
            $arrNCredito["total"]           = ($ncreditos->subtotal0 + $ncreditos->subtotal12 + $ncreditos->impuesto);
            return $arrNCredito;
        }

        if (!is_null($data["fecha_desde"])) {
            $ncreditos = $ncreditos->where('fecha',  '>=', $data["fecha_desde"]);
        }

        if (!is_null($data["fecha_hasta"])) {
            $ncreditos = $ncreditos->where('fecha', '<=', $data["fecha_hasta"]);
        }

        foreach ($ncreditos->get() as $ncredito) {
            array_push($arrNCredito, [
                "id_cliente"      => $ncredito->id_cliente,
                "cliente"         => isset($ncredito->cliente) ? $ncredito->cliente->nombre : $ncredito->id_cliente,
                "tipo"            => $ncredito->tipo,
                "id_asiento"      => $ncredito->id_asiento_cabecera,
                "id_asiento_url"  => url("contable/cliente/nota_credito/editar2") . "/" . $ncredito->id_asiento_cabecera,
                "nro_comprobante" => $ncredito->nro_comprobante,
                "nro_autorizacion" => $ncredito->nro_autorizacion,
                "fecha"           => $ncredito->fecha,
                "concepto"        => $ncredito->concepto,
                "subtotal0"       => $ncredito->subtotal0,
                "subtotal12"      => $ncredito->subtotal12,
                "subtotal"        => ($ncredito->subtotal12 + $ncredito->subtotal0),
                "impuesto"        => $ncredito->tar_iva_12,
                "total"           => ($ncredito->subtotal0 + $ncredito->subtotal12 + $ncredito->impuesto),
            ]);
        }

        return $arrNCredito;
    }

    public static function sqlNCreditoAcreedor($data)
    {
        $credito_acreedores = Ct_Credito_Acreedores::where('id_empresa', Session::get('id_empresa'));
        if ($data["estado"] == "T") {
            $credito_acreedores = $credito_acreedores->where('estado', '<>', NULL);
        } else if ($data["estado"] == 1) {
            $credito_acreedores = $credito_acreedores->where('estado', ">=", 1);
        } else if ($data["estado"] == 0) {
            $credito_acreedores = $credito_acreedores->where('estado', 0);
        }


        if (!is_null($data["fecha_desde"])) {
            $credito_acreedores = $credito_acreedores->where('fecha',  '>=', $data["fecha_desde"]);
        }

        if (!is_null($data["fecha_hasta"])) {
            $credito_acreedores = $credito_acreedores->where('fecha', '<=', $data["fecha_hasta"]);
        }


        return $credito_acreedores->get();
    }

    public static function sqlRetventas($data)
    {
        $cliente_retencion = Ct_Cliente_Retencion::where('id_empresa', Session::get('id_empresa'));
        if ($data["estado"] == "T") {
            $cliente_retencion = $cliente_retencion->where('estado', '<>', NULL);
        } else if ($data["estado"] == 1) {
            $cliente_retencion = $cliente_retencion->where('estado', ">=", 1);
        } else if ($data["estado"] == 0) {
            $cliente_retencion = $cliente_retencion->where('estado', 0);
        }

        if (!is_null($data["fecha_desde"])) {
            $cliente_retencion = $cliente_retencion->where('fecha',  '>=', $data["fecha_desde"]);
        }

        if (!is_null($data["fecha_hasta"])) {
            $cliente_retencion = $cliente_retencion->where('fecha', '<=', $data["fecha_hasta"]);
        }

        $arrRet = [];
        foreach ($cliente_retencion->get() as $value) {

            $ventas     = isset($value->ventas) ? $value->ventas : NULL;
            $numero     = "";
            $cliente    = "";
            $ruc        = "";
            $detalle    = "";
            $por_renta  = 0;
            $por_iva    = "";
            if (!is_null($ventas)) {
                $numero     = $ventas->nro_comprobante;
                $cliente    = isset($ventas->cliente) ? $ventas->cliente->nombre : $ventas->id_cliente;
                $ruc        = isset($ventas->cliente) ? $ventas->id_cliente : "";
                $detalle    = $ventas->concepto;
            }
            foreach ($value->detalle_retencion as $det) {
                if ($det->porcentajer->tipo == 2) {
                    $por_renta  = $det->porcentajer->valor;
                } else if ($det->porcentajer->tipo == 1) {
                    $por_iva    = $det->porcentajer->valor;
                }
            }
            $estado = "ACTIVO";
            $creado_por = isset($value->usuariocrea) ? "{$value->usuariocrea->nombre1} {$value->usuariocrea->apellido1}" : "";

            $anulado_por = "";


            if ($value->estado == 0) {
                $estado = "ANULADO";
                $anulado_por = isset($value->usuariomodif) ? "{$value->usuariomodif->nombre1} {$value->usuariomodif->apellido1}" : "";
            }

            array_push($arrRet, [
                "fecha" => $value->fecha,
                "numero" => $numero,
                "pre_impresa" => $value->id,
                "cliente" => $cliente,
                "ruc" => $ruc,
                "detalle" => $detalle,
                "rfir" => $value->valor_fuente,
                "porc_rfir" => "{$por_renta} %",
                "rfiva" => $value->valor_iva,
                "porc_iva" => $por_iva . " %",
                "estado" => $estado,
                "creado" => $creado_por,
                "anulado" => $anulado_por
            ]);
        }
        return $arrRet;
    }

    public static function _sqlRetCompras($data)
    {
        $accredor_retencion = Ct_Retenciones::where('id_empresa', Session::get('id_empresa'));

        if ($data["estado"] == "T") {
            $accredor_retencion = $accredor_retencion->where('estado', '<>', NULL);
        } else if ($data["estado"] == 1) {
            $accredor_retencion = $accredor_retencion->where('estado', ">=", 1);
        } else if ($data["estado"] == 0) {
            $accredor_retencion = $accredor_retencion->where('estado', 0);
        }

        if (!is_null($data["fecha_desde"])) {
            $accredor_retencion = $accredor_retencion->where('fecha',  '>=', $data["fecha_desde"]);
        }

        if (!is_null($data["fecha_hasta"])) {
            $accredor_retencion = $accredor_retencion->where('fecha', '<=', $data["fecha_hasta"]);
        }

        $arrRet = [];

        foreach ($accredor_retencion->get() as $value) {
            //dd($value, $value->compras, $value->detalle);
            $compras                = isset($value->compras) ? $value->compras : NULL;
            $numero                 = "";
            $acreedor               = "";
            $ruc                    = "";
            $detalle                = "";
            $por_renta              = 0;
            $por_iva                = "";
            $codigo                 = "";
            $tipo_factura           = "";
            $nro_factura            = "";
            $tarifa12               = 0;
            $tarifa0                = 0;
            $subtotal               = 0;
            $iva                    = 0;
            $total                  = 0;
            $id_compra              = "";
            $numero                 = $value->nro_comprobante;

            if (!is_null($compras)) {
                $acreedor       = isset($compras->proveedorf) ? $compras->proveedorf->nombrecomercial : $compras->proveedor;
                $ruc            = isset($compras->proveedorf) ? $compras->proveedor : "";
                $detalle        = $compras->observacion;
                $tarifa12       = $compras->subtotal_12;
                $tarifa0        = $compras->subtotal_0;
                $subtotal       = $compras->subtotal;
                $iva            = $compras->iva_total;
                $total          = $compras->total_final;
                $nro_factura    = $compras->numero;
                $id_compra      = $compras->id;
                $activo = explode(" ", trim($compras->observacion));
                if ($activo[0] == "ACT-FA") {
                    $tipo_factura = "COM-AF";
                } else if ($compras->tipo == 1) {
                    $tipo_factura = 'COM-FA';
                } else {
                    $tipo_factura = 'COM-FACT';
                }
            }
            foreach ($value->detalle as $det) {
                if ($det->porcentajer->tipo == 2) {
                    $por_renta  = $det->porcentajer->valor;
                } else if ($det->porcentajer->tipo == 1) {
                    $por_iva    = $det->porcentajer->valor;
                }

                if (isset($det->porcentajer)) {
                    if ($det->porcentajer->tipo == 2) {
                        $arrCodigo = explode(" ", trim($det->porcentajer->nombre));

                        $de_codigo = De_Codigo_Impuestos::where("codigo", $arrCodigo[0])->first();
                        if (!is_null($de_codigo)) {
                            $codigo = $de_codigo->codigo;
                        } else {
                            $codigo = $det->porcentajer->codigo;
                        }
                    }
                }
            }
            $estado = "ACTIVO";
            $creado_por = isset($value->usuario) ? "{$value->usuario->nombre1} {$value->usuario->apellido1}" : "";

            $anulado_por = "";


            if ($value->estado == 0) {
                $estado = "ANULADO";
                $anulado_por = isset($value->usuariomodif) ? "{$value->usuariomodif->nombre1} {$value->usuariomodif->apellido1}" : "";
            }

            array_push($arrRet, [
                "fecha" => $value->fecha,
                "numero" => $numero,
                "pre_impresa" => $value->id,
                "acreedor" => $acreedor,
                "ruc" => $ruc,
                "detalle" => $detalle,
                "rfir" => $value->valor_fuente,
                "porc_rfir" => "{$por_renta} %",
                "rfiva" => $value->valor_iva,
                "porc_iva" => $por_iva . " %",
                "estado" => $estado,
                "creado" => $creado_por,
                "anulado" => $anulado_por,
                "codigo_fuente" => $codigo,
                "compra_tarifa12" => $tarifa12,
                "compra_tarifa0" => $tarifa0,
                "compra_subtotal" => $subtotal,
                "compra_iva" => $iva,
                "compra_total" => $total,
                "tipo_factura" => $tipo_factura,
                "nro_factura" => $nro_factura,
                "ret_autorizacion" => $value->autorizacion,
                "id_compra" => $id_compra
            ]);
        }
        return $arrRet;
    }

    public static function sqlRetCompras($data)
    {
        $accredor_retencion = Ct_Retenciones::where('id_empresa', Session::get('id_empresa'));
        if ($data["estado"] == "T") {
            $accredor_retencion = $accredor_retencion->where('estado', '<>', NULL);
        } else if ($data["estado"] == 1) {
            $accredor_retencion = $accredor_retencion->where('estado', ">=", 1);
        } else if ($data["estado"] == 0) {
            $accredor_retencion = $accredor_retencion->where('estado', 0);
        }

        if (!is_null($data["fecha_desde"])) {
            $accredor_retencion = $accredor_retencion->where('fecha',  '>=', $data["fecha_desde"]);
        }

        if (!is_null($data["fecha_hasta"])) {
            $accredor_retencion = $accredor_retencion->where('fecha', '<=', $data["fecha_hasta"]);
        }
        // dd($data);
        if (isset($data['id_proveedor']) and !is_null($data['id_proveedor'])) {

            $accredor_retencion = $accredor_retencion->where('id_proveedor', 'like', "%{$data['id_proveedor']}%");
        }

        if (isset($data['secuencia']) and !is_null($data['secuencia'])) {
            $accredor_retencion = $accredor_retencion->where('nro_comprobante', 'like', "%{$data['secuencia']}%");
        }

        $arrRet = [];

        foreach ($accredor_retencion->get() as $value) {
            if (isset($value->compras)) {
                $compras                = $value->compras;
                $numero                 = "";
                $acreedor               = "";
                $ruc                    = "";
                $detalle                = "";
                $por_renta              = 0;
                $por_iva                = "";
                $codigo                 = "";
                $tipo_factura           = "";
                $nro_factura            = "";
                $tarifa12               = 0;
                $tarifa0                = 0;
                $subtotal               = 0;
                $iva                    = 0;
                $total                  = 0;
                $id_compra              = "";
                $numero                 = $value->nro_comprobante;

                if (!is_null($compras)) {
                    $acreedor       = isset($compras->proveedorf) ? $compras->proveedorf->nombrecomercial : $compras->proveedor;
                    $ruc            = isset($compras->proveedorf) ? $compras->proveedor : "";
                    $detalle        = $compras->observacion;
                    $tarifa12       = $compras->subtotal_12;
                    $tarifa0        = $compras->subtotal_0;
                    $subtotal       = $compras->subtotal;
                    $iva            = $compras->iva_total;
                    $total          = $compras->total_final;
                    $nro_factura    = $compras->numero;
                    $id_compra      = $compras->id;
                    $activo = explode(" ", trim($compras->observacion));
                    if ($activo[0] == "ACT-FA") {
                        $tipo_factura = "COM-AF";
                    } else if ($compras->tipo == 1) {
                        $tipo_factura = 'COM-FA';
                    } else {
                        $tipo_factura = 'COM-FACT';
                    }
                }
                $detalle_ret = [];
                foreach ($value->detalle as $det) {
                    // if($det->porcentajer->tipo == 2){
                    //     $por_renta  = $det->porcentajer->valor;
                    // }else if($det->porcentajer->tipo == 1){
                    //     $por_iva    = $det->porcentajer->valor;
                    // }
                    $tipo = "";
                    if (isset($det->porcentajer)) {
                        if ($det->porcentajer->tipo == 2) {
                            $arrCodigo = explode(" ", trim($det->porcentajer->nombre));
                            $de_codigo = De_Codigo_Impuestos::where("codigo", $arrCodigo[0])->first();
                            if (!is_null($de_codigo)) {
                                $codigo = $de_codigo->codigo;
                            } else {
                                $codigo = $det->porcentajer->codigo;
                            }
                        } else {
                            $codigo = $det->porcentajer->codigo;
                        }
                        $tipo = $det->porcentajer->tipo;
                    }

                    $det_arr = ["porct" => "{$det->porcentaje}%", "valor" => $det->totales, "codigo" => $codigo, "tipo" => $tipo, "base_retencion" => $det->base_imponible];
                    array_push($detalle_ret, $det_arr);
                }

                //  dd($detalle_ret);


                $estado = "ACTIVO";
                $creado_por = isset($value->usuario) ? "{$value->usuario->nombre1} {$value->usuario->apellido1}" : "";

                $anulado_por = "";
                if ($value->estado == 0) {
                    $estado = "ANULADO";
                    $anulado_por = isset($value->usuariomodif) ? "{$value->usuariomodif->nombre1} {$value->usuariomodif->apellido1}" : "";
                }

                // array_push($arrRet, ["fecha"=> $value->fecha, "numero"=>$numero,"pre_impresa"=>$value->id, "acreedor"=>$acreedor, "ruc"=>$ruc, "detalle"=>$detalle,
                //                     "rfir"=>$value->valor_fuente, "porc_rfir"=>"{$por_renta} %", "rfiva"=>$value->valor_iva, "porc_iva"=>$por_iva." %", "estado"=>$estado,
                //                     "creado" => $creado_por, "anulado"=> $anulado_por, "codigo_fuente"=>$codigo,"compra_tarifa12"=>$tarifa12 ,"compra_tarifa0"=>$tarifa0,
                //                     "compra_subtotal"=>$subtotal,"compra_iva"=>$iva,"compra_total"=>$total, "tipo_factura"=>$tipo_factura, "nro_factura"=> $nro_factura,
                //                     "ret_autorizacion"=>$value->autorizacion, "id_compra"=>$id_compra]);


                array_push($arrRet, [
                    "fecha" => $value->fecha,
                    "numero" => $numero,
                    "pre_impresa" => $value->id,
                    "acreedor" => $acreedor,
                    "ruc" => $ruc,
                    "detalle" => $detalle,
                    "estado" => $estado,
                    "creado" => $creado_por,
                    "anulado" => $anulado_por,
                    "compra_tarifa12" => $tarifa12,
                    "compra_tarifa0" => $tarifa0,
                    "compra_subtotal" => $subtotal,
                    "compra_iva" => $iva,
                    "compra_total" => $total,
                    "tipo_factura" => $tipo_factura,
                    "nro_factura" => $nro_factura,
                    "ret_autorizacion" => $value->autorizacion,
                    "id_compra" => $id_compra,
                    "retenciones" => $detalle_ret
                ]);
            }
        }
        return $arrRet;
    }

    public static function tableVentas($titles, $datas)
    {
        $table = "";
        $thead = "";
        $tbody = "";
        $status = "error";
        $msj = "No se encontraron resultados";

        $subtotal0total = 0;
        $subtotal12total = 0;
        $subtotaltotal = 0;
        $impuestototal = 0;
        $descuentototal = 0;
        $totalTotal = 0;
        try {
            foreach ($datas as $value) {
                $asiento = "<a class='label label-primary' target='_blank' href='" . url('contable/contabilidad/libro/edit') . "/{$value['id_asiento']}'>{$value['id_asiento']}";
                $ventaUrl = "<a class='label label-warning' target='_blank' href='" . url('/contable/ventas/editar') . "/{$value['id']}'>{$value['id']}</a>";
                $subtotal0total += $value['subtotal0'];
                $subtotal12total += $value['subtotal12'];

                $subtotal = $value['subtotal0'] + $value['subtotal12'];
                $subtotaltotal += $subtotal;

                $impuestototal += $value['impuesto'];
                $total = $subtotal + $value['impuesto'];

                $descuentototal += $value['descuento'];
                $totalTotal += $total;
                $estado  = "<label class='label label-success'>{$value['estado']}</label>";

                if ($value["estado"] == "ANULADA") {
                    $estado  = "<label class='label label-danger'>{$value['estado']}</label>";
                }
                $tbody .= "<tr>
                        <td>{$value['fecha']}</td>
                        <td>{$value['comprobante']}</td>
                        <td>{$value['nro_autorizacion']}</td>
                        <td>{$value['id_cliente']}</td>
                        <td>{$value['cliente']}</td>
                        <td>{$value['seguro']}</td>
                        <td>{$value['tipo']}</td>
                        <td>{$value['detalle']} <b><br>Venta: {$ventaUrl}   <br>#Asiento: {$asiento} </b></td>
                        <td>{$value['subtotal12']}</td>
                        <td>{$value['subtotal0']}</td>
                        <td>{$subtotal}</td>
                        <td>{$value['descuento']}</td>
                        <td>{$value['impuesto']}</td>
                        <td>{$total}</td>
                        <td>{$estado}</td>
                        <td>{$value['creado']}</td>
                        <td>{$value['anulado']}</td>
                      </tr>";
            }


            if ($tbody != "") {
                $msj = "";
                foreach ($titles as $title) {
                    $thead .= "<th>{$title}</th>";
                }

                $tfoot = "<tfoot>
                        <tr>
                            <th colspan='8' style='text-align: end;'>Total</th>
                            <th>{$subtotal12total}</th>
                            <th>{$subtotal0total}</th>
                            <th>{$subtotaltotal}</th>
                            <th>{$descuentototal}</th>
                            <th>{$impuestototal}</th>
                            <th>{$totalTotal}</th>
                        </tr>
                     </tfoot>";



                $table = "<table class='table'>
                            <thead> <tr style='background: rgba(43, 114, 165, 0.3) '> {$thead} <tr> </thead>
                            <tbody>{$tbody}</tbody>
                            {$tfoot}
                        </table>";
                $status = "success";
            }

            return ["status" => $status, "table" => $table, "msj" => $msj];
        } catch (\Exception $e) {
            return ["status" => "error", "table" => "", "msj" => $e->getMessage(), "e" => $e->getLine()];
        }
    }


    public static function tableCompras($titles, $datas)
    {
        try {
            $table = "";
            $thead = "";
            $tbody = "";
            $status = "error";
            $msj = "No se encontraron resultados";

            $subtotal0total = 0;
            $subtotal12total = 0;
            $subtotaltotal = 0;
            $impuestototal = 0;
            $descuentototal = 0;
            $totalTotal = 0;

            foreach ($datas as $value) {
                $asiento = "<a class='label label-primary' target='_blank' href='" . url('contable/contabilidad/libro/edit') . "/{$value['id_asiento']}'>{$value['id_asiento']}";
                $subtotal0total += $value['subtotal0'];
                $subtotal12total += $value['subtotal12'];

                $subtotal = $value['subtotal0'] + $value['subtotal12'];
                // $subtotaltotal += $subtotal;
                $subtotaltotal += $value["subtotal"];

                $impuestototal += $value['impuesto'];
                $total = $subtotal + $value['impuesto'];

                $descuentototal += $value['descuento'];
                // $totalTotal += $total;
                $totalTotal += $value["total"];

                $estado  = "<label class='label label-success'>{$value['estado']}</label>";

                if ($value["estado"] == "ANULADA") {
                    $estado  = "<label class='label label-danger'>{$value['estado']}</label>";
                }

                $tbody .= "<tr>
                        <td>{$value['fecha']}</td>
                        <td>{$value['comprobante']}</td>
                        <td>{$value['nro_autorizacion']}</td>
                        <td>{$value['id_proveedor']}</td>
                        <td>{$value['proveedor']}</td>
                        <td>{$value['tipo']}</td>
                        <td>{$value['detalle']} <b>  <br>#Asiento: {$asiento} </b></td>
                        <td>{$value['subtotal12']}</td>
                        <td>{$value['subtotal0']}</td>
                        <td>{$value["subtotal"]}</td>
                        <td>{$value['descuento']}</td>
                        <td>{$value['impuesto']}</td>
                        <td>{$value['total']}</td>
                        <td>{$estado}</td>
                        <td>{$value['creado']}</td>
                        <td>{$value['anulado']}</td>
                      </tr>";
            }

            if ($tbody != "") {
                $msj = "";
                foreach ($titles as $title) {
                    $thead .= "<th>{$title}</th>";
                }

                $tfoot = "<tfoot>
                        <tr>
                            <th colspan='7' style='text-align: end;'>Total</th>
                            <th>{$subtotal12total}</th>
                            <th>{$subtotal0total}</th>
                            <th>{$subtotaltotal}</th>
                            <th>{$descuentototal}</th>
                            <th>{$impuestototal}</th>
                            <th>{$totalTotal}</th>
                        </tr>
                     </tfoot>";



                $table = "<table class='table'>
                            <thead> <tr style='background: rgba(43, 114, 165, 0.3) '> {$thead} <tr> </thead>
                            <tbody>{$tbody}</tbody>
                            {$tfoot}
                        </table>";
                $status = "success";
            }


            return ["status" => $status, "table" => $table, "msj" => $msj];
        } catch (\Exception $e) {
            return ["status" => "error", "table" => "", "msj" => $e->getMessage(), "e" => $e->getLine()];
        }
    }

    public static function tableRetVentas($titles, $datas)
    {
        $tbody = "";
        $thead = "";
        $status = "error";
        $table = "";
        $msj = "No se encontraron resultados";
        $total_rfiva    = 0;
        $total_rfir     = 0;

        try {
            foreach ($datas as $value) {
                if ($value["estado"] == "ACTIVO") {
                    $estado = "<label class='label label-success'>{$value['estado']}</label>";
                } else {
                    $estado = "<label class='label label-danger'>{$value['estado']}</label>";
                }
                $tbody .= "<tr>
                                <td>{$value['fecha']}</td>
                                <td>{$value['numero']}</td>
                                <td>{$value['pre_impresa']}</td>
                                <td>{$value['cliente']}</td>
                                <td>{$value['ruc']}</td>
                                <td>{$value['detalle']}</td>
                                <td>{$value['rfir']}</td>
                                <td>{$value['porc_rfir']}</td>
                                <td>{$value['rfiva']}</td>
                                <td>{$value['porc_iva']}</td>
                                <td>{$estado}</td>
                                <td>{$value['creado']}</td>
                                <td>{$value['anulado']}</td>
                           </tr>";
                $total_rfir   += floatval($value['rfir']);
                $total_rfiva  += floatval($value['rfiva']);
            }
            if ($tbody != "") {
                $msj = "";
                foreach ($titles as $title) {
                    $thead .= "<th>{$title}</th>";
                }

                $tfoot = "<tfoot>
                                <tr>
                                    <th colspan='6' style='text-align: right;'>TOTAL</th>
                                    <th>{$total_rfir}</th>
                                    <th></th>
                                    <th>{$total_rfiva}</th>
                                </tr>
                         </tfoot>";

                $table = "<table class='table table-success'>
                            <thead> <tr style='background: rgba(43, 114, 165, 0.3) '> {$thead} <tr> </thead>
                            <tbody>{$tbody}</tbody>
                            {$tfoot}
                        </table>";
                $status = "success";
            }

            return ["status" => $status, "table" => $table, "msj" => $msj];
        } catch (\Exception $e) {
            return ["status" => "error", "table" => "", "msj" => $e->getMessage(), "e" => $e->getLine()];
        }
    }

    public static function tableRetCompras($titles, $datas)
    {
        dd($titles, $datas);
        $tbody = "";
        $thead = "";
        $status = "error";
        $table = "";
        $msj = "No se encontraron resultados";
        $total_rfiva    = 0;
        $total_rfir     = 0;

        try {
            // foreach($datas as $value){
            //     if($value["estado"] == "ACTIVO"){
            //         $estado = "<label class='label label-success'>{$value['estado']}</label>";
            //     }else{
            //         $estado = "<label class='label label-danger'>{$value['estado']}</label>";
            //     }
            //     $tbody .= "<tr>
            //                     <td>{$value['fecha']}</td>
            //                     <td>{$value['numero']}</td>
            //                     <td>{$value['pre_impresa']}</td>
            //                     <td>{$value['acreedor']}</td>
            //                     <td>{$value['ruc']}</td>
            //                     <td>{$value['detalle']}</td>
            //                     <td>{$value['rfir']}</td>
            //                     <td>{$value['porc_rfir']}</td>
            //                     <td>{$value['rfiva']}</td>
            //                     <td>{$value['porc_iva']}</td>
            //                     <td>{$estado}</td>
            //                     <td>{$value['creado']}</td>
            //                     <td>{$value['anulado']}</td>
            //                </tr>";
            //     $total_rfir   += floatval($value['rfir']);
            //     $total_rfiva  += floatval($value['rfiva']);
            // }

            // dd($titles, $datas);
            $total_tarifa12 = 0;
            $total_tarifa0 = 0;
            $total_subtotal = 0;
            $total_iva = 0;
            $total_total = 0;
            $total_valor_retenido_fte = 0;
            $total_valor_retenido_iva = 0;

            foreach ($datas as $data) {
                $total_tarifa12           += $data["compra_tarifa12"];
                $total_tarifa0            += $data["compra_tarifa0"];
                $total_subtotal           += $data["compra_subtotal"];
                $total_iva                += $data["compra_iva"];
                $total_total              += $data["compra_total"];
                $datos["data"]  = [
                    $data["pre_impresa"],
                    $data["id_compra"],
                    $data["numero"],
                    $data["ret_autorizacion"],
                    $data["fecha"],
                    $data["acreedor"],
                    $data["ruc"],
                    "",
                    "{$data['tipo_factura']}: {$data['nro_factura']}",
                    $data["compra_tarifa12"],
                    $data["compra_tarifa0"],
                    $data["compra_subtotal"],
                    $data["compra_iva"],
                    $data["compra_total"]
                ];
                $linea_tipo1 = 0;
                $linea_tipo2 = 0;
                foreach ($data["retenciones"] as $det_ret) {

                    $ret_values["data"] = [$det_ret["porct"], $det_ret["valor"], $det_ret["codigo"]];

                    if ($det_ret["tipo"] == 1) {
                        $linea_tipo1++;
                        $ret_values["letra_inicial"]        = "R";
                        $ret_values["letra_final"]          = "R";
                    }
                    if ($det_ret["tipo"] == 2) {
                        $linea_tipo2++;
                        $ret_values["letra_inicial"]        = "O";
                        $ret_values["letra_final"]          = "O";
                    }


                    if (isset($resumen_ret[$det_ret["codigo"]])) {
                        if (isset($resumen_ret[$det_ret["codigo"]]["valor_retenido"])) {
                            $resumen_ret[$det_ret["codigo"]]["valor_retenido"]  += $det_ret["valor"];
                        } else {
                            array_push($resumen_ret[$det_ret["codigo"]], ["valor_retenido" => $det_ret["valor"]]);
                        }
                    } else {
                        $resumen_ret[$det_ret["codigo"]] = [
                            "valor_retenido"    => $det_ret["valor"]
                        ];
                    }
                }
            }

            if ($tbody != "") {
                $msj = "";
                foreach ($titles as $title) {
                    $thead .= "<th>{$title}</th>";
                }

                $tfoot = "<tfoot>
                                <tr>
                                    <th colspan='6' style='text-align: right;'>TOTAL</th>
                                    <th>{$total_rfir}</th>
                                    <th></th>
                                    <th>{$total_rfiva}</th>
                                </tr>
                         </tfoot>";

                $table = "<table class='table table-success'>
                            <thead> <tr style='background: rgba(43, 114, 165, 0.3) '> {$thead} <tr> </thead>
                            <tbody>{$tbody}</tbody>
                            {$tfoot}
                        </table>";
                $status = "success";
            }

            return ["status" => $status, "table" => $table, "msj" => $msj];
        } catch (\Exception $e) {
            return ["status" => "error", "table" => "", "msj" => $e->getMessage(), "e" => $e->getLine()];
        }
    }

    public static function descargarExcel(Request $request)
    {
        if ($request->tipo == "V") {
            $title = ["Fecha", "Número", "Autorización", "CI", "Cliente", "Seguro", "Tipo", "Detalle", "SubTotal12", "SubTotal0", "Subtotal", "Descuento", "Impuesto", "Total", "Estado", "Creado por", "Anulado por"];
            $data = DeclaracionTributacionController::sqlQueryVenta($request->all());
            $excel = DeclaracionTributacionController::excelVentas($title, $data, $request->all());
            return $excel;
        }

        // if ($request->tipo == "C") {
        //     $titleCompra = ["FECHA", "NUMERO", "RUC", "PROVEEDOR", "TIPO COMPROBANTE", "TIPO", "DETALLE", "SUBTOTAL12", "SUBTOTAL0", "SUBTOTAL", "SUBTOTAL12 CON DESCUENTO", "SUBTOTAL0 CON DESCUENTO", "DESCUENTO", "IMPUESTO", "TOTAL", "ESTADO", "CREADO POR", "ANULADO POR"];
        //     $data = DeclaracionTributacionController::sqlQueryCompra($request->all());
        //     $excel = DeclaracionTributacionController::excelVentas($titleCompra, $data, $request->all());
        //     return $excel;
        // }

        if ($request->tipo == "T") {
            /* Para ventas */
            $titles = [];
            /********************TITULO PARA VENTAS  **********************/
            $titles["ventas"] = ["CLASIFICACION", "SISTEMA", "FECHA", "CLIENTE", "RUC", "AUT. SRI", "OBSERVACION", "DOC.", "Nº FACTURA", "BASE 0%", "BASE 12%", "SUBTOTAL", "IVA", "TOTAL",/* "0,0100","0,03", "0,08", "0,70", "1,00", "TOTAL A RECIBIR", "# RETENCION", "AUTOR #"*/];
            /************************************************************ */

            /**********************TITULOS PARA COMPRAS*****************************/
            $titles["compras"] = ["", "N. Comp. SISTEMA", "Fecha", "PROVEEDOR", "RUC", "AUT. SRI", "OBSERVACION", "DOC.", "# FACTURA", "Tarifa 0%", "Base Impo12%", "SUBTOTAL", "0,12", "TOTAL FACTURA"];
            /**********************************************************************/

            /*************************Consultas a las tablas**************************/
            $dataVentas = DeclaracionTributacionController::sqlQueryVenta($request->all());
            $dataCompras = DeclaracionTributacionController::sqlQueryCompra($request->all());
            $dataNCliente = DeclaracionTributacionController::sqlNCreditoCliente($request->all());
            $dataNCAcreedor = DeclaracionTributacionController::sqlNCreditoAcreedor($request->all());

            $data["ventas"] = $dataVentas;
            $data["compras"] = $dataCompras;
            $data["nCliente"] = $dataNCliente;
            $data["nCAcreedor"] = $dataNCAcreedor;
            /*************************************************************************/

            $excel = DeclaracionTributacionController::excelTodo($titles, $data, $request->all());
        }

        if ($request->tipo == "C") {
            $title = ["Fecha", "Número", "Ruc", "Proveedor", "Tipo Comprobante", "Tipo", "Detalle", "SubTotal12", "SubTotal0", "SubTotal", "Descuento", "Impuesto", "Total", "Estado", "Creado Por", "Anulado Po"];
            $data = DeclaracionTributacionController::sqlQueryCompra($request->all());
            $excel = DeclaracionTributacionController::excelCompras($title, $data, $request->all());
            return $excel;
        }

        if ($request->tipo == "RC") {
            $titlesRetVentas = ["Fecha", "Número", "Preimpresa", "Cliente", "RUC", "Detalle", "Total RFIR.", "%", "Total RFIVA", "%", "Estado", "Creado por", "Anulado por"];
            $data   = DeclaracionTributacionController::sqlRetventas($request->all());
            $excel = DeclaracionTributacionController::excelRetVentas($titlesRetVentas, $data, $request->all());
        }

        if ($request->tipo == "RA") {
            $titles = [
                "COD COMPR",
                "#COMP",
                "# RETENCION",
                "AUTORIZACION",
                "FECHA",
                "PROVEEDOR",
                "RUC",
                "SERIE",
                "FACTURA",
                "TARIFA 12%",
                "TARIFA 0%",
                "SUBTOTAL",
                "IVA",
                "TOTAL",
                "% RET FTE",
                "VALOR RETENIDO",
                "CODIGO",
                "% RET IVA",
                "VALOR RETENIDO",
                "CODIGO",
                "CADUCIDAD"
            ];
            $data   = DeclaracionTributacionController::sqlRetCompras($request->all());
            $excel = DeclaracionTributacionController::excelRetCompras($titles, $data, $request->all());
        }
    }

    public static function excelVentas($title, $datas, $info)
    {
        Excel::create('Reporte FC. VENTAS', function ($excel) use ($title, $datas, $info) {
            $excel->sheet('Reporte FC. VENTAS', function ($sheet) use ($title, $datas, $info) {



                $comienzo = 1;

                $sheet->mergeCells('A' . $comienzo . ':' . 'Q' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) {

                    $empresa = Empresa::find(Session::get('id_empresa'));
                    $cell->setValue($empresa->razonsocial);
                    $cell->setFontColor('#FCFCFC');
                    $cell->setBackground('#34495e');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('14');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;


                $sheet->mergeCells('A' . $comienzo . ':' . 'Q' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) use ($info) {
                    $cell->setValue("INFORME DE VENTAS DESDE (" . date("d/m/Y", strtotime($info['fecha_desde'])) . " - " . date("d/m/Y", strtotime($info['fecha_hasta'])) . ")");
                    $cell->setFontColor('#FCFCFC');
                    $cell->setBackground('#34495e');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('14');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;

                $title["data"] = $title;
                $title["comienzo"] = 3;
                $title["background-color"] = "#34495e";
                $title["color"] = "#ffffff";
                excelCreate::details($sheet, $title);


                $subtotal0total = 0;
                $subtotal12total = 0;
                $subtotaltotal = 0;
                $impuestototal = 0;
                $descuentototal = 0;
                $totalTotal = 0;
                $comienzo = 4;


                foreach ($datas as $value) {

                    $subtotal0total += $value['subtotal0'];
                    $subtotal12total += $value['subtotal12'];

                    $subtotal = $value['subtotal0'] + $value['subtotal12'];
                    $subtotaltotal += $subtotal;

                    $impuestototal += $value['impuesto'];
                    $total = $subtotal + $value['impuesto'];

                    $descuentototal += $value['descuento'];
                    $totalTotal += $total;

                    $arrData["background-color"] = "#FFFFFF";
                    if ($value["estado"] == "ANULADA") {
                        $arrData["background-color"]  = "#FDD4D4";
                    }

                    $arrData["font-weight"] = "bold";
                    $arrData["data"] = [
                        $value['fecha'],
                        $value['comprobante'],
                        $value['nro_autorizacion'],
                        $value['id_cliente'],
                        $value['cliente'],
                        $value['seguro'],
                        $value['tipo'],
                        "{$value['detalle']} Venta: {$value['id']} Asiento: {$value['id_asiento']}",
                        number_format($value['subtotal12'], 2,  '.', ''),
                        number_format($value['subtotal0'], 2,  '.', ''),
                        number_format($subtotal, 2,  '.', ''),
                        number_format($value['descuento'], 2,  '.', ''),
                        number_format($value['impuesto'], 2,  '.', ''),
                        number_format($total, 2,  '.', ''),
                        $value['estado'],
                        $value['creado'],
                        $value['anulado']
                    ];
                    $arrData["comienzo"] = $comienzo;
                    excelCreate::details($sheet, $arrData);
                    $comienzo++;

                    // if($info["detalles"])
                    if (isset($info["detalles"])) {
                        $detalles = DeclaracionTributacionController::detallesVenta($value);
                        $cab_detalles["data"] = ['Detalles'];
                        $cab_detalles["num_inicial"]    = $comienzo;
                        $cab_detalles["num_final"]      = $comienzo;
                        $cab_detalles["letra_inicial"]  = "A";
                        $cab_detalles["letra_final"]    = "E";
                        $cab_detalles["direccion"]      = "normal";
                        $cab_detalles["font-size"]      = 10;
                        $cab_detalles['background-color'] = "#95B3D7";
                        $excel_datos = excelCreate::mergeCells($sheet, $cab_detalles);
                        $comienzo++;

                        $titulo_detalle["data"] = ['Codigo producto', 'Nombre producto', 'Bodega', 'Cantidad', 'Precio'];
                        $titulo_detalle["comienzo"] = $comienzo;
                        $titulo_detalle['background-color'] = "#DCE6F1";

                        excelCreate::details($sheet, $titulo_detalle);
                        $comienzo++;


                        foreach ($detalles as $detalle_venta) {
                            $datos_detalle_ventas["data"] = [$detalle_venta['cod_prod'], $detalle_venta['nomb_prod'], $detalle_venta['bod_prod'], $detalle_venta['can_prod'], $detalle_venta['pre_prod']];
                            $datos_detalle_ventas["comienzo"] = $comienzo;
                            excelCreate::details($sheet, $datos_detalle_ventas);
                            $comienzo++;
                        }
                    }

                    // $comienzo++;
                }

                $footer["data"] = ["", "", "", "", "", "", "", "Total", number_format($subtotal12total, 2,  '.', ''), number_format($subtotal0total, 2,  '.', ''), number_format($subtotaltotal, 2,  '.', ''), number_format($descuentototal, 2,  '.', ''), number_format($impuestototal, 2,  '.', ''), number_format($totalTotal, 2,  '.', '')];

                $footer["comienzo"] = $comienzo;
                excelCreate::details($sheet, $footer);
                $comienzo++;
            });
        })->export('xlsx');
    }

    public static function detallesVenta($datas)
    {
        $datos = [];
        $detalle_venta = Ct_detalle_venta::where('id_ct_ventas', $datas['id'])->where('estado', 1)->get();

        foreach ($detalle_venta as $value) {

            $array_detalle = [
                "cod_prod" => $value->id_ct_productos,
                "nomb_prod" => $value->nombre,
                "bod_prod" => $value->bodega,
                "can_prod" => $value->cantidad,
                "pre_prod" => $value->precio,
            ];

            array_push($datos, $array_detalle);
        }
        return $datos;
        // dd($datos);
    }


    public static function excelCompras($title, $datas, $info)
    {
        Excel::create('Reporte FC. COMPRAS', function ($excel) use ($title, $datas, $info) {
            $excel->sheet('Reporte FC. COMPRAS', function ($sheet) use ($title, $datas, $info) {

                $comienzo = 1;

                $sheet->mergeCells('A' . $comienzo . ':' . 'Q' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) {

                    $empresa = Empresa::find(Session::get('id_empresa'));
                    $cell->setValue($empresa->razonsocial);
                    $cell->setFontColor('#FCFCFC');
                    $cell->setBackground('#34495e');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('14');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;


                $sheet->mergeCells('A' . $comienzo . ':' . 'Q' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) use ($info) {
                    $cell->setValue("INFORME DE COMPRAS DESDE (" . date("d/m/Y", strtotime($info['fecha_desde'])) . " - " . date("d/m/Y", strtotime($info['fecha_hasta'])) . ")");
                    $cell->setFontColor('#FCFCFC');
                    $cell->setBackground('#34495e');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('14');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;

                $title["data"] = $title;
                $title["comienzo"] = 3;
                $title["background-color"] = "#34495e";
                $title["color"] = "#ffffff";
                excelCreate::details($sheet, $title);

                $subtotal0total = 0;
                $subtotal12total = 0;
                $subtotaltotal = 0;
                $impuestototal = 0;
                $descuentototal = 0;
                $totalTotal = 0;
                $comienzo = 4;


                foreach ($datas as $value) {

                    $subtotal0total += $value['subtotal0'];
                    $subtotal12total += $value['subtotal12'];

                    $subtotal = $value['subtotal0'] + $value['subtotal12'];
                    // $subtotaltotal += $subtotal;
                    $subtotaltotal += $value["subtotal"];

                    $impuestototal += $value['impuesto'];
                    $total = $subtotal + $value['impuesto'];

                    $descuentototal += $value['descuento'];
                    $totalTotal += $value["total"];

                    $arrData["background-color"] = "#FFFFFF";
                    if ($value["estado"] == "ANULADA") {
                        $arrData["background-color"]  = "#FDD4D4";
                    }

                    $arrData["data"] = [
                        $value['fecha'],
                        $value['comprobante'],
                        $value['nro_autorizacion'],
                        $value['id_proveedor'],
                        $value['proveedor'],
                        $value['tipo'],
                        $value['detalle'] . " Asiento: " . $value['id_asiento'],
                        $value['subtotal12'],
                        $value['subtotal0'],
                        $value["subtotal"],
                        $value['descuento'],
                        $value['impuesto'],
                        $value["total"],
                        $value["estado"],
                        $value['creado'],
                        $value['anulado'],
                    ];
                    $arrData["comienzo"] = $comienzo;
                    excelCreate::details($sheet, $arrData);
                    $comienzo++;
                }


                $footer["data"] = ["", "", "", "", "", "", "Total", number_format($subtotal12total, 2,  '.', ''), number_format($subtotal0total, 2,  '.', ''), number_format($subtotaltotal, 2,  '.', ''), number_format($descuentototal, 2,  '.', ''), number_format($impuestototal, 2,  '.', ''), number_format($totalTotal, 2,  '.', '')];
                $footer["comienzo"] = $comienzo;
                excelCreate::details($sheet, $footer);
                $comienzo++;
            });
        })->export('xlsx');
    }

    public static function excelTodo($titles, $data, $info)
    {

        Excel::create('DECL. TRIBUTACION', function ($excel) use ($titles, $data, $info) {
            $excel->sheet('DECL. TRIBUTACION', function ($sheet) use ($titles, $data, $info) {

                $retencion = Ct_Porcentaje_Retenciones::where('estado', 1)->orderBy("tipo", "desc")->get();

                /************************LAS CONSULTAS DE COMPRAS **********************/
                $comienzo = 1;
                $compra =  DeclaracionTributacionController::excelTodoCompra($sheet, $titles["compras"], $data["compras"], $comienzo, $retencion);

                $comienzo = $compra["comienzo"] + 2;
                $ncredito_acreedor =  DeclaracionTributacionController::excelNCreditoAcreedor($sheet, $data["nCAcreedor"], $comienzo);

                $comienzo = $ncredito_acreedor["comienzo"] + 3;
                $resumenCompra = DeclaracionTributacionController::resumenCompra($sheet, $compra, $comienzo, $retencion, $ncredito_acreedor);

                /**********************************************************************/

                /************************LAS CONSULTAS DE VENTAS **********************/
                $comienzo = $resumenCompra["comienzo"] + 4;
                $ventas =  DeclaracionTributacionController::excelTodoVenta($sheet, $titles["ventas"], $data["ventas"], $comienzo, $retencion);

                $comienzo = $ventas["comienzo"] + 2;
                $ncredito = DeclaracionTributacionController::excelNCreditoCliente($sheet, $data["nCliente"], $comienzo);
                $comienzo = $ncredito["comienzo"] + 4;

                $notasCredito =  DeclaracionTributacionController::excelResumenVenta($sheet, $ventas, $comienzo, $data["nCliente"]);

                /**********************************************************************/

                $sheet->setColumnFormat(array(
                    "E" => "@"
                ));
            });
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(50)->setAutosize(false);
        })->export('xlsx');
    }

    public static function excelTodoVenta($sheet, $titles, $data, $inicial, $consulta)
    {
        $arrayTipoRetencion = [];
        $arrayVenta = [];

        $arrayNCredito["subtotal0"]  = 0;
        $arrayNCredito["subtotal12"]  = 0;
        $arrayNCredito["subtotal"]  = 0;
        $arrayNCredito["impuesto"]  = 0;
        $arrayNCredito["total"]  = 0;

        foreach ($consulta as $tipo) {
            // array_push($arrayTipoRetencion, [$tipo->id => ""]);
            $arrayTipoRetencion[$tipo->id] = "";
            array_push($titles, $tipo->nombre);
        }


        array_push($titles, "TOTAL A RECIBIR", "# RETENCION", "AUTOR #");

        $comienzo  = $inicial;


        $sheet->mergeCells('A' . $comienzo . ':' . 'AI' . $comienzo);
        $sheet->cell('A' . $comienzo, function ($cell) {
            $cell->setValue("VENTAS");
            $cell->setFontColor('#FCFCFC');
            $cell->setBackground('#34495e');
            $cell->setFontWeight('bold');
            $cell->setFontSize('15');
            $cell->setAlignment('center');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $comienzo++;


        $title["data"] = $titles;
        $title["comienzo"] = $comienzo;
        $title["background-color"] = "#34495e";
        $title["color"] = "#ffffff";
        excelCreate::details($sheet, $title);
        $comienzo++;

        $subtotal0total = 0;
        $subtotal12total = 0;
        $subtotaltotal = 0;
        $impuestototal = 0;
        $descuentototal = 0;
        $totalTotal = 0;
        //$comienzo = 4;

        foreach ($data as $value) {
            $subtotal0total += $value['subtotal0'];
            $subtotal12total += $value['subtotal12'];

            $subtotal = $value['subtotal0'] + $value['subtotal12'];
            $subtotaltotal += $subtotal;

            $impuestototal += $value['impuesto'];
            $total = $subtotal + $value['impuesto'];

            $descuentototal += $value['descuento'];
            $totalTotal += $total;

            $arrData["background-color"] = "#FFFFFF";
            if ($value["estado"] == "ANULADA") {
                $arrData["background-color"]  = "#FDD4D4";
            }


            $arrData["data"] = [
                $value['tipo'],
                $value["id_asiento"],
                $value['fecha'],
                $value['cliente'],
                $value['id_cliente'],
                $value['nro_autorizacion'],
                $value['detalle'],
                $value['tipo'],
                $value['comprobante'],
                number_format($value['subtotal0'], 2,  '.', ''),
                number_format($value['subtotal12'], 2,  '.', ''),
                number_format($subtotal, 2,  '.', ''),
                number_format($value['impuesto'], 2,  '.', ''),
                number_format($total, 2,  '.', '')
            ];

            $retencion = DeclaracionTributacionController::buscaRetencionVentas($value["id"], $arrayTipoRetencion);
            $total_retencion = 0;
            foreach ($retencion["data"] as $ret) {
                array_push($arrData["data"], $ret);
                if ($ret != "") {
                    $total_retencion += $ret;
                }
            }

            $total_recibir = $total - $total_retencion;

            array_push($arrData["data"], number_format($total_recibir, 2,  '.', ''), $retencion["n_retencion"], $retencion["aut_retencion"]);

            $arrData["comienzo"] = $comienzo;
            excelCreate::details($sheet, $arrData);
            $comienzo++;

            if (isset($arrayVenta[$value['tipo']])) {
                $arrayVenta[$value['tipo']]["subtotal0"]    +=  $value["subtotal0"];
                $arrayVenta[$value['tipo']]["subtotal12"]   +=  $value["subtotal12"];
                $arrayVenta[$value['tipo']]["subtotal"]     +=  $subtotal;
                $arrayVenta[$value['tipo']]["impuesto"]     +=  $value["impuesto"];
                $arrayVenta[$value['tipo']]["descuento"]    +=  $value["descuento"];
                $arrayVenta[$value['tipo']]["total"]        +=  $total;
            } else {
                $arrayVenta[$value['tipo']] = [
                    "subtotal0"    =>  $value["subtotal0"],
                    "subtotal12"   =>  $value["subtotal12"],
                    "subtotal"     =>  $subtotal,
                    "impuesto"     =>  $value["impuesto"],
                    "descuento"    =>  $value["descuento"],
                    "total"        =>  $total,
                ];
            }


            $nc_clientes = Ct_Nota_Credito_Clientes::where("id_factura", $value["id"])->where("estado", 1)->get();
            if (count($nc_clientes) > 0) {
                foreach ($nc_clientes as $nc_cliente) {
                    if (isset($arrayNCredito["subtotal0"])) {
                        $arrayNCredito["subtotal0"]     +=  $nc_cliente->subtotal0;
                        $arrayNCredito["subtotal12"]    +=  $nc_cliente->subtotal12;
                        $arrayNCredito["subtotal"]      +=  ($nc_cliente->subtotal12 + $nc_cliente->subtotal0);
                        $arrayNCredito["impuesto"]      +=  $nc_cliente->impuesto;
                        $arrayNCredito["total"]         +=  ($nc_cliente->subtotal0 + $nc_cliente->subtotal12 + $nc_cliente->impuesto);
                    }
                }
            }

            // dd($arrayNCredito);
        }

        $foot["data"] = ["", "", "", "", "", "", "", "", "TOTAL", number_format($subtotal0total, 2,  '.', ''), number_format($subtotal12total, 2,  '.', ''), number_format($subtotaltotal, 2,  '.', ''), number_format($impuestototal, 2,  '.', ''), number_format($totalTotal, 2,  '.', '')];
        $foot["comienzo"] = $comienzo;
        excelCreate::details($sheet, $foot);
        $comienzo++;
        return ["comienzo" => $comienzo, "arrayVenta" => $arrayVenta, "arrayNCredito" => $arrayNCredito];
    }

    public static function excelNCreditoCliente($sheet, $data, $inicial)
    {
        $comienzo  = $inicial;


        $sheet->mergeCells('A' . $comienzo . ':' . 'N' . $comienzo);
        $sheet->cell('A' . $comienzo, function ($cell) {
            $cell->setValue("NOTAS DE CREDITO CLIENTE");
            $cell->setFontColor('#FCFCFC');
            $cell->setBackground('#34495e');
            $cell->setFontWeight('bold');
            $cell->setFontSize('15');
            $cell->setAlignment('center');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $comienzo++;


        $titles["data"] = ["CLASIFICACION", "SISTEMA", "FECHA", "CLIENTE", "RUC", "AUT. SRI", "OBSERVACION", "DOC.", "Nº FACTURA", "BASE 0%", "BASE 12%", "SUBTOTAL", "IVA", "TOTAL"];

        $titles["comienzo"] = $comienzo;
        $titles["background-color"] = "#34495e";
        $titles["color"] = "#FFFFFF";
        excelCreate::details($sheet, $titles);
        $comienzo++;


        foreach ($data as $value) {
            $nc_data["data"] = [$value["tipo"], "", $value["fecha"], $value["cliente"], $value["id_cliente"], $value["nro_autorizacion"], $value["concepto"], $value["tipo"], $value["nro_comprobante"], $value["subtotal0"], $value["subtotal12"], $value["subtotal"], $value["impuesto"], $value["total"]];
            $nc_data["comienzo"] = $comienzo;
            excelCreate::details($sheet, $nc_data);
            $comienzo++;
        }
        return ["comienzo" => $comienzo];
    }

    public static function excelResumenVenta($sheet, $data, $inicial, $dataNc = [])
    {
        $comienzo  = $inicial;
        $sheet->mergeCells('A' . $comienzo . ':' . 'F' . $comienzo);
        $sheet->cell('A' . $comienzo, function ($cell) {
            $cell->setValue("RESUMEN DE VENTAS");
            $cell->setFontColor('#FCFCFC');
            $cell->setBackground('#34495e');
            $cell->setFontWeight('bold');
            $cell->setFontSize('15');
            $cell->setAlignment('center');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $comienzo++;

        $arrayNCredito = $data["arrayNCredito"];


        $restSubtotal0 = 0;
        $restSubtotal12 = 0;
        $restSubtotal = 0;
        $restImpuesto = 0;
        $restTotal = 0;

        $titles["data"] = ["", "0%", "12%",    "subtotal",    "IVA", "Total"];
        $titles["comienzo"] = $comienzo;
        $titles["background-color"] = "#34495e";
        $titles["color"] = "#CBCBF5";
        excelCreate::details($sheet, $titles);
        $comienzo++;

        // dd($data["arrayVenta"], $data);

        foreach ($data["arrayVenta"] as $key => $arrventa) {
            $body["data"] = [$key,  $arrventa["subtotal0"], $arrventa["subtotal12"], $arrventa["subtotal"], $arrventa["impuesto"], $arrventa["total"]];
            $body["comienzo"] = $comienzo;
            excelCreate::details($sheet, $body);
            $comienzo++;

            $restSubtotal0 += $arrventa["subtotal0"];
            $restSubtotal12 += $arrventa["subtotal12"];
            $restSubtotal += $arrventa["subtotal"];
            $restImpuesto += $arrventa["impuesto"];
            $restTotal += $arrventa["total"];
        }
        $ncSubtotal0 = 0;
        $ncSubtotal12 = 0;
        $ncSubtotal = 0;
        $ncImpuesto = 0;
        $ncTotalTotal = 0;
        foreach ($dataNc as $ncNuew) {
            $ncSubtotal0 += $ncNuew["subtotal0"];
            $ncSubtotal12 += $ncNuew["subtotal12"];
            $ncSubtotal += ($ncNuew["subtotal0"] + $ncNuew["subtotal12"]);
            $ncImpuesto += $ncNuew["impuesto"];
            $ncTotalTotal += $ncNuew["total"];
        }
        //$nc["data"] = ["CLI-NC",  $arrayNCredito["subtotal0"], $arrayNCredito["subtotal12"], $arrayNCredito["subtotal"], $arrayNCredito["impuesto"], $arrayNCredito["total"]];
        $nc["data"] = ["CLI-NC",  $ncSubtotal0, $ncSubtotal12, $ncSubtotal, $ncImpuesto, $ncTotalTotal];

        $nc["comienzo"] = $comienzo;
        excelCreate::details($sheet, $nc);
        $comienzo++;



        /*
        $foot["data"] = [
            "TOTAL", ($restSubtotal0 - $arrayNCredito["subtotal0"]), ($restSubtotal12 - $arrayNCredito["subtotal12"]), ($restSubtotal - $arrayNCredito["subtotal"]),
            ($restImpuesto - $arrayNCredito["impuesto"]), ($restTotal - $arrayNCredito["total"])
        ];*/

        $foot["data"] = [
            "TOTAL",
            ($restSubtotal0 - $ncSubtotal0),
            ($restSubtotal12 - $ncSubtotal12),
            ($restSubtotal - $ncSubtotal),
            ($restImpuesto - $ncImpuesto),
            ($restTotal - $ncTotalTotal)
        ];
        $foot["comienzo"] = $comienzo;
        excelCreate::details($sheet, $foot);
        $comienzo++;

        return ["comienzo" => $comienzo];
    }

    public static function excelTodoCompra($sheet, $titles, $data, $inicial, $consulta)
    {
        $arrayTipoRetencion = [];
        $arrayCompra = [];
        $arrayResumen = [];
        foreach ($consulta as $tipo) {
            $arrayTipoRetencion[$tipo->id] = "";
            array_push($titles, $tipo->nombre);
        }

        array_push($titles, "PAGO", "Nº Ret.", "Nº AUT.", "observacion", "CODIGO");

        $comienzo  = $inicial;

        $sheet->mergeCells('A' . $comienzo . ':' . 'AK' . $comienzo);
        $sheet->cell('A' . $comienzo, function ($cell) {
            $cell->setValue("COMPRAS");
            $cell->setFontColor('#FCFCFC');
            $cell->setBackground('#34495e');
            $cell->setFontWeight('bold');
            $cell->setFontSize('15');
            $cell->setAlignment('center');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $comienzo++;

        $title["data"] = $titles;
        $title["comienzo"] = $comienzo;
        $title["background-color"] = "#34495e";
        $title["color"] = "#ffffff";
        excelCreate::details($sheet, $title);
        $comienzo++;

        $subtotal0total = 0;
        $subtotal12total = 0;
        $subtotaltotal = 0;
        $impuestototal = 0;
        $descuentototal = 0;
        $totalTotal = 0;

        foreach ($data as $value) {
            $valores = DeclaracionTributacionController::calcularDetallesCompras($value);
            $value['subtotal0'] = $valores['subtotal0'];
            $value['subtotal12'] = $valores['subtotal12'];


            $subtotal0total += $value['subtotal0'];
            $subtotal12total += $value['subtotal12'];

            $subtotal = $value['subtotal0'] + $value['subtotal12'];
            // $subtotaltotal += $subtotal;
            $subtotaltotal += $value["subtotal"];

            $impuestototal += $value['impuesto'];
            $total = $subtotal + $value['impuesto'];

            $descuentototal += $value['descuento'];
            // $totalTotal += $total;
            $totalTotal += $value["total"];

            $arrData["background-color"] = "#FFFFFF";
            if ($value["estado"] == "ANULADA") {
                $arrData["background-color"]  = "#FDD4D4";
            }


            $arrData["data"] = [
                $value["tipo"],
                $value["id_asiento"],
                $value["fecha"],
                $value["proveedor"],
                $value["id_proveedor"],
                $value["nro_autorizacion"],
                $value["detalle"],
                $value["tipo_comprobante"],
                $value["comprobante"],
                $value["subtotal0"],
                $value['subtotal12'],
                $value["subtotal"],
                $value["impuesto"],
                $value["total"]
            ];
            $codRepetidos = [];
            $retencion = DeclaracionTributacionController::buscaRetencionCompras($value["id"], $arrayTipoRetencion);
            //Validamos que si encuentra uno que tenga 2 tipos y los guardamos en un array para tenerlo como registro
            //y que nolo tome en cuenta para cuando se vuelva a repetir
            if ($retencion["retornoTipo"]["validacion"]) {
                array_push($codRepetidos, $retencion['retornoTipo']["id_det_ret"]);
                $arrData["background-color"] = "#FFE194";
            }


            // if($value["id"] == 18590){
            //     dd($retencion, $arrData, $consulta);
            // }
            $total_retencion = 0;
            foreach ($retencion["data"] as $ret) {
                array_push($arrData["data"], $ret);
                if ($ret != "") {
                    $total_retencion += $ret;
                }
            }
            foreach ($consulta as $valNew) {
                if ($valNew->tipo == "2") {
                    $valor_retencion = $retencion["data"][$valNew->id];
                    if ($valor_retencion == "") {
                        $valor_retencion = 0;
                    }
                    $arrayResumen[$value['tipo']][$valNew->id] = isset($arrayResumen[$value['tipo']][$valNew->id]) ? $arrayResumen[$value['tipo']][$valNew->id] +  $valor_retencion : $valor_retencion;
                }
            }

            // if ($retencion["retornoTipo"]["validacion"]) {
            //     $total_pago = $retencion["retornoTipo"]["valor"];
            // }else{
            $total_pago = $total - $total_retencion;
            // }


            // array_push($arrData["data"],    ($total_pago, 2,  '.', ''), $retencion["n_retencion"] , $retencion["aut_retencion"]);
            array_push(
                $arrData["data"],
                $total_pago,
                $retencion["n_retencion"],
                $retencion["aut_retencion"],
                $retencion["observacion"],
                $retencion["codigo"]
            );
            $arrData["comienzo"] = $comienzo;
            if ($retencion["retornoTipo"]["validacion"]) {
                $arrData["data"][13] = $retencion["retornoTipo"]["valor"];
                // dd($arrData,  $retencion);

            }

            // dd($value);


            excelCreate::details($sheet, $arrData);
            $comienzo++;


            if ($retencion["retornoTipo"]["validacion"]) {
                $arrData["data"] = ["", "", "", "", "", "", "", "", "", "", "", "", "", ""];
                $totalRet = $total_retencion;
                do {
                    $retencion = DeclaracionTributacionController::mostrarFuenteRepetidos($value["id"], $arrayTipoRetencion, $codRepetidos);

                    if ($retencion["retornoTipo"]["validacion"]) {
                        array_push($codRepetidos, $retencion['retornoTipo']["id_det_ret"]);
                    }

                    if ($retencion["status"] == "success") {
                        $total_retencion = 0;
                        foreach ($retencion["data"] as $ret) {
                            array_push($arrData["data"], $ret);
                            if ($ret != "") {
                                $total_retencion += $ret;
                            }
                        }
                        foreach ($consulta as $valNew) {
                            if ($valNew->tipo == "2") {
                                $valor_retencion = $retencion["data"][$valNew->id];
                                if ($valor_retencion == "") {
                                    $valor_retencion = 0;
                                }
                                $arrayResumen[$value['tipo']][$valNew->id] = isset($arrayResumen[$value['tipo']][$valNew->id]) ? $arrayResumen[$value['tipo']][$valNew->id] +  $valor_retencion : $valor_retencion;
                            }
                        }

                        $total_pago = $total - $total_retencion - $totalRet;
                        $totalRet += $total_retencion;
                        // $arrData[13] =
                        // $arrData[13] = $retencion["retornoTipo"]["valor"];

                        // array_push($arrData["data"],    ($total_pago, 2,  '.', ''), $retencion["n_retencion"] , $retencion["aut_retencion"]);
                        array_push(
                            $arrData["data"],
                            $total_pago,
                            $retencion["n_retencion"],
                            $retencion["aut_retencion"],
                            $retencion["observacion"],
                            $retencion["codigo"]
                        );
                        $arrData["comienzo"] = $comienzo;
                        $arrData["background-color"] = "#FFE194";
                        $arrData["data"][13] = $retencion["retornoTipo"]["valor"];

                        excelCreate::details($sheet, $arrData);
                        $comienzo++;
                    }
                } while (!$retencion["retornoTipo"]["validacion"]);
            }


            if (isset($arrayCompra[$value['tipo']])) {
                $arrayCompra[$value['tipo']]["subtotal0"]    +=  $value["subtotal0"];
                $arrayCompra[$value['tipo']]["subtotal12"]   +=  $value["subtotal12"];
                $arrayCompra[$value['tipo']]["subtotal"]     +=  $value["subtotal"];
                $arrayCompra[$value['tipo']]["impuesto"]     +=  $value["impuesto"];
                $arrayCompra[$value['tipo']]["descuento"]    +=  $value["descuento"];
                $arrayCompra[$value['tipo']]["total"]        +=  $value["total"];
            } else {
                $arrayCompra[$value['tipo']] = [
                    "subtotal0"    =>  $value["subtotal0"],
                    "subtotal12"   =>  $value["subtotal12"],
                    "subtotal"     =>  $value["subtotal"],
                    "impuesto"     =>  $value["impuesto"],
                    "descuento"    =>  $value["descuento"],
                    "total"        =>  $value["total"],
                ];
            }
        }

        $foot["data"] = ["", "", "", "", "", "", "", "", "TOTAL", number_format($subtotal0total, 2,  '.', ''), number_format($subtotal12total, 2,  '.', ''), number_format($subtotaltotal, 2,  '.', ''), number_format($impuestototal, 2,  '.', ''), number_format($totalTotal, 2,  '.', '')];
        $foot["comienzo"] = $comienzo;
        excelCreate::details($sheet, $foot);
        $comienzo++;
        return ["comienzo" => $comienzo, "arrayCompra" => $arrayCompra, "arrayResumen" => $arrayResumen];
    }

    public static function calcularDetallesCompras($data)
    {
        $subtotal12 = 0;
        $subtotal0 = 0;
        $detalles = Ct_detalle_compra::where('id_ct_compras', $data['id'])->get();
        // dd($detalles);
        foreach ($detalles as $detalle) {
            if ($detalle->iva == 1) {
                $subtotal12 += $detalle->extendido;
            } else {
                $subtotal0 += $detalle->extendido;
            }
        }

        return ["subtotal12" => $subtotal12, "subtotal0" => $subtotal0];
    }

    public static function excelNCreditoAcreedor($sheet, $data, $inicial)
    {
        $comienzo  = $inicial;

        $subtotal0total = 0;
        $subtotal12total = 0;
        $subtotalTotal = 0;
        $impuestoTotal = 0;
        $totalTotal = 0;

        if (count($data) == 0) {
            return ["comienzo" => $comienzo, "subtotal0total" => $subtotal0total, "subtotal12total" => $subtotal12total, "subtotaltotal" => $subtotalTotal, "impuestoTotal" => $impuestoTotal, "totalTotal" => $totalTotal];
        }

        $sheet->mergeCells('A' . $comienzo . ':' . 'J' . $comienzo);
        $sheet->cell('A' . $comienzo, function ($cell) {
            $cell->setValue("NOTAS DE CREDITO ACREEDOR");
            $cell->setFontColor('#FCFCFC');
            $cell->setBackground('#34495e');
            $cell->setFontWeight('bold');
            $cell->setFontSize('15');
            $cell->setAlignment('center');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $comienzo++;
        $title["data"] = ["Fecha", "Secuencia", "Proveedor", "Factura", "Detalle", "Subtotal 0", "Subtotal 12", "Subtotal", "Impuesto", "Total"];
        $title["comienzo"] = $comienzo;
        $title["background-color"] = "#34495e";
        $title["color"] = "#ffffff";
        excelCreate::details($sheet, $title);
        $comienzo++;


        foreach ($data as $value) {
            $proveedor = isset($value->proveedor) ? $value->proveedor->razonsocial : $value->id_proveedor;
            $factura = isset($value->factura) ? $value->factura->numero : "";
            $total = $value->subtotal + $value->impuesto;
            $arrBody["data"] = [
                $value->fecha,
                "{$value->secuencia}-{$value->serie}",
                $proveedor,
                $factura,
                $value->concepto,
                $value->subtotal_0,
                $value->subtotal_12,
                $value->subtotal,
                $value->impuesto,
                $total
            ];

            $arrBody["comienzo"] = $comienzo;
            // $arrBody["background-color"] = "#34495e";
            // $arrBody["color"] = "#ffffff";
            excelCreate::details($sheet, $arrBody);
            $comienzo++;

            $subtotal0total += $value->subtotal_0;
            $subtotal12total +=  $value->subtotal_12;
            $subtotalTotal += $value->subtotal;
            $impuestoTotal +=  $value->impuesto;
            $totalTotal += $total;
        }

        $foot["data"] = ["", "", "", "", "TOTAL", $subtotal0total, $subtotal12total, $subtotalTotal, $impuestoTotal, $totalTotal];
        $foot["comienzo"] = $comienzo;
        // $foot["background-color"] = "#34495e";
        $foot["color"] = "#ffffff";
        excelCreate::details($sheet, $foot);
        $comienzo++;

        return ["comienzo" => $comienzo, "subtotal0total" => $subtotal0total, "subtotal12total" => $subtotal12total, "subtotaltotal" => $subtotalTotal, "impuestoTotal" => $impuestoTotal, "totalTotal" => $totalTotal];
    }

    public static function excelRetVentas($title, $datas, $info)
    {
        Excel::create('Reporte RT. VENTAS', function ($excel) use ($title, $datas, $info) {
            $excel->sheet('Reporte RT. VENTAS', function ($sheet) use ($title, $datas, $info) {

                $comienzo = 1;

                $sheet->mergeCells('A' . $comienzo . ':' . 'M' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) {

                    $empresa = Empresa::find(Session::get('id_empresa'));
                    $cell->setValue($empresa->razonsocial);
                    $cell->setFontColor('#FCFCFC');
                    $cell->setBackground('#34495e');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('14');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;


                $sheet->mergeCells('A' . $comienzo . ':' . 'M' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) use ($info) {
                    $cell->setValue("INFORME RETENCIONES DE VENTAS DESDE (" . date("d/m/Y", strtotime($info['fecha_desde'])) . " - " . date("d/m/Y", strtotime($info['fecha_hasta'])) . ")");
                    $cell->setFontColor('#FCFCFC');
                    $cell->setBackground('#34495e');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('14');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;

                $title["data"] = $title;
                $title["comienzo"] = $comienzo;
                $title["background-color"] = "#34495e";
                $title["color"] = "#ffffff";
                excelCreate::details($sheet, $title);
                $comienzo++;

                $total_rfir = 0;
                $total_rfiva = 0;
                foreach ($datas as $value) {

                    $data["data"] = [
                        $value['fecha'],
                        $value['numero'],
                        $value['pre_impresa'],
                        $value['cliente'],
                        $value['ruc'],
                        $value['detalle'],
                        $value['rfir'],
                        $value['porc_rfir'],
                        $value['rfiva'],
                        $value['porc_iva'],
                        $value['estado'],
                        $value['creado'],
                        $value['anulado']
                    ];
                    $data["comienzo"] = $comienzo;
                    excelCreate::details($sheet, $data);

                    $total_rfir   += floatval($value['rfir']);
                    $total_rfiva  += floatval($value['rfiva']);

                    $comienzo++;
                }
                $footer["data"] = ["", "", "", "", "", "Total", $total_rfir, "",  $total_rfiva, "", "", "", ""];
                $footer["comienzo"] = $comienzo;
                excelCreate::details($sheet, $footer);
                $comienzo++;
            });
        })->export('xlsx');
    }

    public static function _excelRetCompras($title, $datas, $info)
    {
        Excel::create('Reporte RT. COMPRAS', function ($excel) use ($title, $datas, $info) {
            $excel->sheet('Reporte RT. COMPRAS', function ($sheet) use ($title, $datas, $info) {

                $comienzo = 1;

                $sheet->mergeCells('A' . $comienzo . ':' . 'M' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) {

                    $empresa = Empresa::find(Session::get('id_empresa'));
                    $cell->setValue($empresa->razonsocial);
                    $cell->setFontColor('#FCFCFC');
                    $cell->setBackground('#34495e');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('14');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;


                $sheet->mergeCells('A' . $comienzo . ':' . 'M' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) use ($info) {
                    $cell->setValue("INFORME RETENCIONES DE VENTAS DESDE (" . date("d/m/Y", strtotime($info['fecha_desde'])) . " - " . date("d/m/Y", strtotime($info['fecha_hasta'])) . ")");
                    $cell->setFontColor('#FCFCFC');
                    $cell->setBackground('#34495e');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('14');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;

                $title["data"] = $title;
                $title["comienzo"] = $comienzo;
                $title["background-color"] = "#34495e";
                $title["color"] = "#ffffff";
                excelCreate::details($sheet, $title);
                $comienzo++;

                $total_rfir = 0;
                $total_rfiva = 0;
                foreach ($datas as $value) {

                    $data["data"] = [
                        $value['fecha'],
                        $value['numero'],
                        $value['pre_impresa'],
                        $value['acreedor'],
                        $value['ruc'],
                        $value['detalle'],
                        $value['rfir'],
                        $value['porc_rfir'],
                        $value['rfiva'],
                        $value['porc_iva'],
                        $value['estado'],
                        $value['creado'],
                        $value['anulado']
                    ];
                    $data["comienzo"] = $comienzo;
                    excelCreate::details($sheet, $data);

                    $total_rfir   += floatval($value['rfir']);
                    $total_rfiva  += floatval($value['rfiva']);

                    $comienzo++;
                }
                $footer["data"] = ["", "", "", "", "", "Total", $total_rfir, "",  $total_rfiva, "", "", "", ""];
                $footer["comienzo"] = $comienzo;
                excelCreate::details($sheet, $footer);
                $comienzo++;
            });
        })->export('xlsx');
    }

    public static function presentadoexcelRetCompras($title, $datas, $info)
    {
        Excel::create('Reporte RT. COMPRAS', function ($excel) use ($title, $datas, $info) {
            $excel->sheet('Reporte RT. COMPRAS', function ($sheet) use ($title, $datas, $info) {

                $comienzo = 1;

                $sheet->mergeCells('A' . $comienzo . ':' . 'U' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) {
                    $empresa = Empresa::find(Session::get('id_empresa'));
                    $cell->setValue($empresa->razonsocial);
                    $cell->setFontColor('#FCFCFC');
                    $cell->setBackground('#34495e');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('14');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;
                $sheet->mergeCells('A' . $comienzo . ':' . 'U' . $comienzo);
                $sheet->cell('A' . $comienzo, function ($cell) use ($info) {
                    $cell->setValue("INFORME RETENCIONES DE COMPRAS DESDE (" . date("d/m/Y", strtotime($info['fecha_desde'])) . " - " . date("d/m/Y", strtotime($info['fecha_hasta'])) . ")");
                    $cell->setFontColor('#FCFCFC');
                    $cell->setBackground('#34495e');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('14');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo++;

                $cabecera["data"]           = $title;
                $cabecera["comienzo"]       = $comienzo;
                $cabecera["background-color"]  = "#34495e";
                $cabecera["color"]             = "#ffffff";
                excelCreate::details($sheet, $cabecera);
                $comienzo++;

                $total_tarifa12           = 0;
                $total_tarifa0            = 0;
                $total_subtotal           = 0;
                $total_iva                = 0;
                $total_total              = 0;
                $total_valor_retenido_fte = 0;
                $total_valor_retenido_iva = 0;

                $resumen_ret = [];
                foreach ($datas as $data) {
                    // dd($data);
                    $total_tarifa12           += $data["compra_tarifa12"];
                    $total_tarifa0            += $data["compra_tarifa0"];
                    $total_subtotal           += $data["compra_subtotal"];
                    $total_iva                += $data["compra_iva"];
                    $total_total              += $data["compra_total"];
                    $total_valor_retenido_fte += $data["rfir"];
                    $total_valor_retenido_iva += $data["rfiva"];

                    $datos["data"]  = [
                        $data["pre_impresa"],
                        $data["id_compra"],
                        $data["numero"],
                        $data["fecha"],
                        $data["acreedor"],
                        $data["ruc"],
                        "",
                        "{$data['tipo_factura']}: {$data['nro_factura']}",
                        $data["compra_tarifa12"],
                        $data["compra_tarifa0"],
                        $data["compra_subtotal"],
                        $data["compra_iva"],
                        $data["compra_total"],
                        $data["porc_rfir"],
                        $data["rfir"],
                        $data["codigo_fuente"],
                        $data["porc_iva"],
                        $data["rfiva"],
                        "",
                        $data["ret_autorizacion"],
                        ""
                    ];
                    $datos["comienzo"] = $comienzo;
                    excelCreate::details($sheet, $datos);
                    $comienzo++;


                    if (isset($resumen_ret[$data["codigo_fuente"]])) {
                        if (isset($resumen_ret[$data["codigo_fuente"]]["total_compra"])) {
                            $resumen_ret[$data["codigo_fuente"]]["total_compra"]    += $data["compra_total"];
                            $resumen_ret[$data["codigo_fuente"]]["valor_retenido"]  += $data["rfir"];
                        } else {
                            array_push($resumen_ret[$data["codigo_fuente"]], ["total_compra" => $data["compra_total"], "valor_retenido" => $data["rfir"]]);
                        }
                    } else {
                        $resumen_ret[$data["codigo_fuente"]] = [
                            "total_compra" => $data["compra_total"],
                            "valor_retenido" => $data["rfir"]
                        ];
                    }
                }


                $footer["data"] = ["", "", "", "", "", "", "", "TOTAL", $total_tarifa12, $total_tarifa0, $total_subtotal, $total_iva, $total_total, "", $total_valor_retenido_fte, "", "", $total_valor_retenido_iva];
                $footer["comienzo"] = $comienzo;
                $footer["font-weight"] = "bold";
                excelCreate::details($sheet, $footer);
                $comienzo += 3;
                $cabecera["data"]                 = ["RESUMEN RETENCION"];
                $cabecera["num_inicial"]          = $comienzo;
                $cabecera["num_final"]            = $comienzo;
                $cabecera["letra_inicial"]        = "A";
                $cabecera["letra_final"]          = "E";
                $cab_resumen = excelCreate::mergeCells($sheet, $cabecera);
                $comienzo++;

                $total_fact_resumen = 0;
                $total_ret_resumen = 0;
                foreach ($resumen_ret as $key => $value) {
                    $de_codigo = De_Codigo_Impuestos::where("codigo", $key)->first();
                    $data_resumen["data"]                 = [$key, $key, $value["total_compra"], !is_null($de_codigo) ? $de_codigo->porcentaje . "%" : '', $value["valor_retenido"]];
                    $data_resumen["comienzo"]             = $comienzo;
                    $total_fact_resumen += $value["total_compra"];
                    $total_ret_resumen  += $value["valor_retenido"];
                    excelCreate::details($sheet, $data_resumen);
                    $comienzo++;
                }

                $foot_resumen["data"] = ["", "", $total_fact_resumen, "", $total_ret_resumen];
                $foot_resumen["comienzo"] = $comienzo;
                $foot_resumen["font-weight"] = "bold";
                excelCreate::details($sheet, $foot_resumen);
            });
        })->export('xlsx');
    }

    public static function excelRetCompras($title, $datas, $info)
    {
        Excel::create('Reporte RT. COMPRAS', function ($excel) use ($title, $datas, $info) {
            $excel->sheet('Reporte RT. COMPRAS', function ($sheet) use ($title, $datas, $info) {
                $comienzo = 1;
                $empresa = Empresa::find(Session::get('id_empresa'));
                $cabecera["data"]                 = [$empresa->razonsocial];
                $cabecera["num_inicial"]          = $comienzo;
                $cabecera["num_final"]            = $comienzo;
                $cabecera["letra_inicial"]        = "A";
                $cabecera["letra_final"]          = "U";
                $cabecera["font-size"]            = 14;
                $cabecera["background-color"]     = "#34495e";
                $cabecera["color"]                = "#FFFFFF";
                excelCreate::mergeCells($sheet, $cabecera);
                $comienzo++;

                $cabecera["data"]                 = ["INFORME RETENCIONES DE COMPRAS DESDE (" . date("d/m/Y", strtotime($info['fecha_desde'])) . " - " . date("d/m/Y", strtotime($info['fecha_hasta'])) . ")"];
                $cabecera["num_inicial"]          = $comienzo;
                $cabecera["num_final"]            = $comienzo;
                excelCreate::mergeCells($sheet, $cabecera);
                $comienzo++;

                $cabecera["data"]               = $title;
                $cabecera["comienzo"]           = $comienzo;
                $cabecera["background-color"]   = "#34495e";
                $cabecera["color"]              = "#ffffff";
                excelCreate::details($sheet, $cabecera);
                $comienzo++;

                $total_tarifa12           = 0;
                $total_tarifa0            = 0;
                $total_subtotal           = 0;
                $total_iva                = 0;
                $total_total              = 0;

                $resumen_ret = [];

                $newRet = [];
                foreach ($datas as $data) {
                    $total_tarifa12           += $data["compra_tarifa12"];
                    $total_tarifa0            += $data["compra_tarifa0"];
                    $total_subtotal           += $data["compra_subtotal"];
                    $total_iva                += $data["compra_iva"];
                    $total_total              += $data["compra_total"];
                    $datos["data"]  = [
                        $data["pre_impresa"],
                        $data["id_compra"],
                        $data["numero"],
                        $data["ret_autorizacion"],
                        $data["fecha"],
                        $data["acreedor"],
                        $data["ruc"],
                        "",
                        "{$data['tipo_factura']}: {$data['nro_factura']}",
                        $data["compra_tarifa12"],
                        $data["compra_tarifa0"],
                        $data["compra_subtotal"],
                        $data["compra_iva"],
                        $data["compra_total"]
                    ];
                    $datos["num_inicial"]          = $comienzo;
                    $datos["letra_inicial"]        = "A";
                    $datos["letra_final"]          = "A";
                    $datos["direccion"]             = "normal";

                    $linea_tipo1 = 0;
                    $linea_tipo2 = 0;
                    foreach ($data["retenciones"] as $det_ret) {

                        $ret_values["direccion"]             = "normal";
                        $ret_values["data"] = [$det_ret["porct"], $det_ret["valor"], $det_ret["codigo"]];

                        if ($det_ret["tipo"] == 1) {
                            $linea_tipo1++;
                            $ret_values["letra_inicial"]        = "R";
                            $ret_values["letra_final"]          = "R";
                            $ret_values["num_inicial"]          = ($comienzo + $linea_tipo1) - 1;
                            $ret_values["num_final"]            = ($comienzo + $linea_tipo1) - 1;
                        }
                        if ($det_ret["tipo"] == 2) {
                            $linea_tipo2++;
                            $ret_values["letra_inicial"]        = "O";
                            $ret_values["letra_final"]          = "O";
                            $ret_values["num_inicial"]          = ($comienzo + $linea_tipo2) - 1;
                            $ret_values["num_final"]            = ($comienzo + $linea_tipo2) - 1;
                        }
                        $ret_values["background-color"]   = "#ffffff";
                        $ret_values["color"]              = "#000000";
                        // dd($data["estado"]);
                        if ($data["estado"] != "ACTIVO") {
                            $ret_values["background-color"]   = "#E06469";
                            $ret_values["color"]   = "#ffffff";
                            // dd($datos);
                        }
                        excelCreate::mergeCells($sheet, $ret_values);

                        if (isset($resumen_ret[$det_ret["codigo"]])) {
                            if (isset($resumen_ret[$det_ret["codigo"]]["valor_retenido"])) {
                                // $resumen_ret[$det_ret["codigo"]]["total_compra"]    += $det_ret["compra_total"];
                                if ($data["estado"] == "ACTIVO") {
                                    $resumen_ret[$det_ret["codigo"]]["valor_retenido"]  += $det_ret["valor"];
                                }
                            } else {
                                if ($data["estado"] == "ACTIVO") {
                                    array_push($resumen_ret[$det_ret["codigo"]], ["valor_retenido" => $det_ret["valor"]]);
                                }
                            }
                        } else {
                            if ($data["estado"] == "ACTIVO") {
                                $resumen_ret[$det_ret["codigo"]] = [
                                    // "total_compra"      =>$det_ret["compra_total"] ,
                                    "valor_retenido"    => $det_ret["valor"]
                                ];
                            }
                        }

                        // $newRet["letra_inicial"]        = "L";
                        // $newRet["letra_final"]          = "L";
                        // $newRet["num_inicial"]          = $comienzo;
                        // $newRet["num_final"]            = $comienzo;
                        // $newRet["data"]= [$det_ret["base_retencion"]];
                        // excelCreate::mergeCells($sheet, $newRet);

                    }
                    if ($linea_tipo2 > $linea_tipo1) {
                        $comienzo = $comienzo + $linea_tipo2 - 1;
                    } else {
                        $comienzo = $comienzo + $linea_tipo1 - 1;
                    }
                    $datos["num_final"]            =  $comienzo;

                    $datos["background-color"]   = "#ffffff";
                    $datos["color"]              = "#000000";
                    // dd($data["estado"]);
                    if ($data["estado"] != "ACTIVO") {
                        $datos["background-color"]   = "#E06469";
                        $datos["color"]   = "#ffffff";
                        // dd($datos);
                    }

                    excelCreate::mergeCells($sheet, $datos);

                    $comienzo++;
                }

                $footer["data"] = ["", "", "", "", "", "", "", "TOTAL", $total_tarifa12, $total_tarifa0, $total_subtotal, $total_iva, $total_total];
                $footer["comienzo"] = $comienzo;
                $footer["font-weight"] = "bold";
                excelCreate::details($sheet, $footer);
                $comienzo += 3;

                $cabecera["data"]                 = ["RESUMEN RETENCION"];
                $cabecera["num_inicial"]          = $comienzo;
                $cabecera["num_final"]            = $comienzo;
                $cabecera["letra_inicial"]        = "A";
                $cabecera["letra_final"]          = "D";
                $cab_resumen = excelCreate::mergeCells($sheet, $cabecera);
                $comienzo++;

                $total_fact_resumen = 0;
                $total_ret_resumen = 0;
                foreach ($resumen_ret as $key => $value) {
                    $de_codigo = De_Codigo_Impuestos::where("codigo", $key)->first();
                    $data_resumen["data"]                 = [$key, $key, !is_null($de_codigo) ? $de_codigo->porcentaje . "%" : '', $value["valor_retenido"]];
                    $data_resumen["comienzo"]             = $comienzo;
                    // $total_fact_resumen += $value["total_compra"];
                    $total_ret_resumen  += $value["valor_retenido"];
                    excelCreate::details($sheet, $data_resumen);
                    $comienzo++;
                }

                $foot_resumen["data"] = ["", "", "", $total_ret_resumen];
                $foot_resumen["comienzo"] = $comienzo;
                $foot_resumen["font-weight"] = "bold";
                excelCreate::details($sheet, $foot_resumen);

                // dd($resumen_ret);

            });
        })->export('xlsx');
    }

    public static function resumenCompra($sheet, $data, $inicial, $consulta, $ncredito_acreedor)
    {
        $comienzo  = $inicial;


        $sheet->mergeCells('A' . $comienzo . ':' . 'S' . $comienzo);
        $sheet->cell('A' . $comienzo, function ($cell) {
            $cell->setValue("COSTOS Y GASTOS");
            $cell->setFontColor('#FCFCFC');
            $cell->setBackground('#34495e');
            $cell->setFontWeight('bold');
            $cell->setFontSize('15');
            $cell->setAlignment('center');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');
        });
        $comienzo++;

        $restSubtotal0 = 0;
        $restSubtotal12 = 0;
        $restSubtotal = 0;
        $restImpuesto = 0;
        $restTotal = 0;

        $arrParaGuia = [];

        $titles["data"] = ["", "0%", "12%",    "subtotal",    "IVA", "Total"];
        foreach ($consulta as $tipo) {
            if ($tipo->tipo == 2) {
                array_push($arrParaGuia, $tipo->id);
                array_push($titles["data"], $tipo->nombre);
            }
        }

        $titles["comienzo"] = $comienzo;
        $titles["background-color"] = "#34495e";
        $titles["color"] = "#CBCBF5";
        excelCreate::details($sheet, $titles);
        $comienzo++;


        foreach ($data["arrayCompra"] as $key => $arrcompra) {
            $body["data"] = [$key,  $arrcompra["subtotal0"], $arrcompra["subtotal12"], $arrcompra["subtotal"], $arrcompra["impuesto"], $arrcompra["total"]];
            $body["comienzo"] = $comienzo;


            foreach ($arrParaGuia as $guiaRet) {
                array_push($body["data"], $data["arrayResumen"][$key][$guiaRet]);
            }

            excelCreate::details($sheet, $body);
            $comienzo++;

            $restSubtotal0 += $arrcompra["subtotal0"];
            $restSubtotal12 += $arrcompra["subtotal12"];
            $restSubtotal += $arrcompra["subtotal"];
            $restImpuesto += $arrcompra["impuesto"];
            $restTotal += $arrcompra["total"];
        }

        $nc["data"] = ["ACR-NC", $ncredito_acreedor["subtotal0total"], $ncredito_acreedor["subtotal12total"], $ncredito_acreedor["subtotaltotal"], $ncredito_acreedor["impuestoTotal"], $ncredito_acreedor["totalTotal"]];
        $nc["comienzo"] = $comienzo;
        excelCreate::details($sheet, $nc);
        $comienzo++;


        $foot["data"] = ["TOTAL", ($restSubtotal0 - $ncredito_acreedor["subtotal0total"]), ($restSubtotal12 - $ncredito_acreedor["subtotal12total"]), ($restSubtotal - $ncredito_acreedor["subtotaltotal"]), ($restImpuesto - $ncredito_acreedor["impuestoTotal"]), ($restTotal - $ncredito_acreedor["totalTotal"])];
        $foot["comienzo"] = $comienzo;
        excelCreate::details($sheet, $foot);
        $comienzo++;
        return ["comienzo" => $comienzo];
    }

    public static function excelTodoNCredito($sheet, $data, $inicial)
    {
        $comienzo  = $inicial;
        $titles["data"] = ["", "0%", "12%",    "subtotal",    "IVA", "Total"];
        $titles["comienzo"] = $comienzo;
        $titles["background-color"] = "#2b72a5";
        $titles["color"] = "#CBCBF5";
        excelCreate::details($sheet, $titles);
        $comienzo++;

        foreach ($data["arrayVenta"] as $key => $arrventa) {
            $body["data"] = [$key,  $arrventa["subtotal0"], $arrventa["subtotal12"], $arrventa["subtotal"], $arrventa["impuesto"], $arrventa["total"]];
            $body["comienzo"] = $comienzo;
            excelCreate::details($sheet, $body);
            $comienzo++;
        }
    }

    public static function buscaRetencionVentas($id_venta, $arrRetencion)
    {
        $n_retencion = "";
        $aut_retencion = "";
        $retencion = Ct_Cliente_Retencion::where("id_factura", $id_venta)->where("estado", 1)->first();
        $copyArrRetencion = $arrRetencion;
        if (!is_null($retencion)) {
            $n_retencion = $retencion->nro_comprobante;
            $aut_retencion = $retencion->autorizacion;

            $copyArrRetencion = $arrRetencion;
            foreach ($retencion->detalle_retencion as $det) {
                if (isset($copyArrRetencion[$det->id_porcentaje])) {
                    $copyArrRetencion[$det->id_porcentaje] = $det->totales;
                }
            }
        }
        return ["status" => "success", "data" => $copyArrRetencion, "n_retencion" => $n_retencion, "aut_retencion" => $aut_retencion];
    }

    public static function buscaRetencionCompras($id_compra, $arrRetencion)
    {
        $n_retencion = "";
        $aut_retencion = "";
        $observacion = "";
        $retencion = Ct_Retenciones::where("id_compra", $id_compra)->where("estado", 1)->first();
        $codigo = "";
        $copyArrRetencion = $arrRetencion;
        $dosTipos = false;
        $auxValorRet = "";
        $retornoTipo = ["id_det_ret" => "", "validacion" => false, "valor" => 0];

        if (!is_null($retencion)) {
            $n_retencion = $retencion->nro_comprobante;
            $aut_retencion = $retencion->nro_autorizacion;
            $observacion = $retencion->descripcion;
            foreach ($retencion->detalle as $det) {
                // if (isset($copyArrRetencion[$det->id_porcentaje])) {
                //     $copyArrRetencion[$det->id_porcentaje] = $det->totales;
                // }

                if (isset($det->porcentajer)) {
                    if ($det->porcentajer->tipo == 2) {
                        if (!$dosTipos) {
                            $arrCodigo = explode(" ", trim($det->porcentajer->nombre));
                            $de_codigo = De_Codigo_Impuestos::where("codigo", $arrCodigo[0])->first();
                            if (!is_null($de_codigo)) {
                                $codigo = $de_codigo->codigo;
                            } else {
                                $codigo = $det->porcentajer->codigo;
                            }
                            if (isset($copyArrRetencion[$det->id_porcentaje])) {
                                $copyArrRetencion[$det->id_porcentaje] = $det->totales;
                            }
                        }
                        if ($dosTipos) {
                            $retornoTipo["validacion"] = true;
                        } else {
                            $dosTipos = true;
                            $retornoTipo["id_det_ret"] = $det->id;
                            $retornoTipo["valor"] = $det->base_imponible;
                        }
                    } else {
                        if (isset($copyArrRetencion[$det->id_porcentaje])) {
                            $copyArrRetencion[$det->id_porcentaje] = $det->totales;
                        }
                    }
                }
            }
        }
        return [
            "status" => "success",
            "data" => $copyArrRetencion,
            "n_retencion" => $n_retencion,
            "aut_retencion" => $aut_retencion,
            "observacion" => $observacion,
            "codigo" => $codigo,
            "retornoTipo" => $retornoTipo,
            "auxValorRet" => $auxValorRet
        ];
    }

    public static function mostrarFuenteRepetidos($id_compra, $arrRetencion, $codRepetidos)
    {
        $n_retencion = "";
        $aut_retencion = "";
        $observacion = "";
        $retencion = Ct_Retenciones::where("id_compra", $id_compra)->where("estado", 1)->first();
        $codigo = "";
        $copyArrRetencion = $arrRetencion;
        $dosTipos = false;
        $retornoTipo = ["id_det_ret" => "", "validacion" => false];

        if (!is_null($retencion)) {
            $n_retencion = $retencion->nro_comprobante;
            $aut_retencion = $retencion->nro_autorizacion;
            $observacion = $retencion->descripcion;
            foreach ($retencion->detalle as $det) {
                $existe = false;
                if (isset($det->porcentajer)) {
                    if ($det->porcentajer->tipo == 2) {

                        foreach ($codRepetidos as $codRep) {
                            if ($codRep == $det->id) {
                                $existe = true;
                            } else {
                                $retornoTipo["validacion"] = true;
                                $retornoTipo["id_det_ret"] = $det->id;
                                $retornoTipo["valor"] = $det->base_imponible;
                            }
                        }

                        if (!$existe) {
                            $arrCodigo = explode(" ", trim($det->porcentajer->nombre));
                            $de_codigo = De_Codigo_Impuestos::where("codigo", $arrCodigo[0])->first();
                            if (!is_null($de_codigo)) {
                                $codigo = $de_codigo->codigo;
                            } else {
                                $codigo = $det->porcentajer->codigo;
                            }
                            if (isset($copyArrRetencion[$det->id_porcentaje])) {
                                $copyArrRetencion[$det->id_porcentaje] = $det->totales;
                            }

                            return [
                                "status" => "success",
                                "data" => $copyArrRetencion,
                                "n_retencion" => $n_retencion,
                                "aut_retencion" => $aut_retencion,
                                "observacion" => $observacion,
                                "codigo" => $codigo,
                                "retornoTipo" => $retornoTipo
                            ];
                        }
                    }
                }
            }
        }
        return ["status" => "error", "retornoTipo" => $retornoTipo];
    }

    public static function tableTodo() {}
}
