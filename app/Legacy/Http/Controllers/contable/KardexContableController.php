<?php

namespace Sis_medico\Http\Controllers\contable;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\InvKardex;
use Sis_medico\Producto;
use Sis_medico\Empresa;
use Session;
use Excel;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_ventas;
use Sis_medico\Ct_productos;
use Sis_medico\Bodega;
use Sis_medico\Ct_productos_insumos;

class KardexContableController extends Controller
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

        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde = date('Y-m-d', strtotime($request['fecha_desde']));
            $fecha_hasta = date('Y-m-d', strtotime($request['fecha_hasta']));
        } else {
            $fecha_desde = date('d/m/Y');
            $fecha_hasta = date('d/m/Y');
        }

        $id_empresa     = Session::get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $id_producto = 0;
        if (isset($request['id_producto'])) {
            $id_producto = $request['id_producto'];
        }
        $id_bodega = 0;
        if (isset($request['id_bodega'])) {
            $id_bodega = $request['id_bodega'];
        }
        // dd($fecha_desde);
        $productos  = Ct_productos::where('id_empresa', Session::get('id_empresa'))->orderby('id', 'desc')->get();
        $bodegas    = Bodega::all();
        return view('contable/kardex_contable/index', [
            'productos' => $productos, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'id_producto' => $id_producto, 
            'bodegas'=>$bodegas, 'id_bodega' => $id_bodega
        ]);
    }

    public function show(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa     = Session::get('id_empresa');
        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $rfecha_desde           = $request['fecha_desde'];
            $request['fecha_desde'] = str_replace('/', '-', $request['fecha_desde']);
            $timestamp              = \Carbon\Carbon::parse($request['fecha_desde'])->timestamp;
            $fecha_desde            = date('Y-m-d', $timestamp);

            $rfecha_hasta           = $request['fecha_hasta'];
            $request['fecha_hasta'] = str_replace('/', '-', $request['fecha_hasta']);
            $timestamp              = \Carbon\Carbon::parse($request['fecha_hasta'])->timestamp;
            $fecha_hasta            = date('Y-m-d', $timestamp);
        } else {
            $fecha_desde = date('Y/m/d');
            $fecha_hasta = date('Y/m/d');
        }
        $id_producto     = 0;
        if (isset($request['id_producto'])) {
            $id_producto = $request['id_producto'];
        }
        $id_bodega      = 0;
        if (isset($request['id_bodega'])) {
            $id_bodega  = $request['id_bodega'];
        }
        $kardex = array(); $prod = array(); $skardex = array();
        if (isset($request['id_producto']) and $request['id_producto'] != "") {
            
            $productos = Ct_productos::where('id', $request['id_producto'])
                                ->where('id_empresa', Session::get('id_empresa'))
                                ->get(); 
        } else {
            $productos      = Ct_productos::get(); 
        } 
            foreach ($productos as $value) {
                $productos = Ct_productos_insumos::where('id_producto', $value->id)->get();
                $wproductos = array();
                foreach ($productos as $r) {
                    array_push($wproductos, $r->id_insumo);
                } 
                if (!empty($wproductos)) {
                    $kardex = InvKardex::whereIn('id_producto', $wproductos)
                            ->where('estado', '!=', '2')
                            // ->where('id_empresa', Session::get('id_empresa'))
                            ->where('fecha','>=' , $fecha_desde . " 00:00:00")
                            ->where('fecha','<=' , $fecha_hasta . " 23:59:59");
                            if (isset($request['id_bodega']) and $request['id_bodega'] != "") {
                                $kardex = $kardex->where('id_bodega', $request['id_bodega']);
                            }
                            $kardex = $kardex->orderBy('fecha', 'ASC')
                                            ->orderBy('id', 'ASC')
                                            ->get(); 
                
                    // dd($kardex);
                    $karx = array();    $detalles = array();
                    $karx['codigo'] = $value->codigo;
                    $karx['nombre'] = $value->nombre;
                    $karx['descripcion'] = $value->nombre;

                    # SALDOS 
                    $sal_kardex = InvKardex::whereIn('id_producto', $wproductos)
                            ->where('estado', '!=', '2')
                            // ->where('id_empresa', Session::get('id_empresa'))
                            ->where('fecha','<' , $fecha_desde . " 00:00:00") ;
                            if (isset($request['id_bodega']) and $request['id_bodega'] != "") {
                                $sal_kardex = $sal_kardex->where('id_bodega', $request['id_bodega']);
                            }
                    $sal_kardex = $sal_kardex->select('id_producto', DB::raw('SUM(coalesce(cantidad,0)) as tcantidad'), DB::raw('MAX(coalesce(valor_unitario,0)) as tvaluni'), DB::raw('SUM(coalesce(total,0)) as total')) 
                                                ->groupBy('id_producto')
                                                ->first();  
                    if (isset($sal_kardex->tcantidad) and $sal_kardex->tcantidad!=null) {
                        $karx['tcantidad'] = $sal_kardex->tcantidad;
                    } else {
                        $karx['tcantidad'] = 0;
                    }
                    if (isset($sal_kardex->tvaluni) and $sal_kardex->tvaluni!=null) {
                        $karx['tvaluni'] = $sal_kardex->tvaluni;
                    } else {
                        $karx['tvaluni'] = 0;
                    }
                    $karx['ttotal'] = $karx['tcantidad'] * $karx['tvaluni'];

                    foreach ($kardex as $row) { 
                        $referencia = ""; 
                        $referencia = $row->referencia;  
                        //  REFERENCIA COMPRAS
                        if (isset($row->documento_origen->cabecera)and$referencia == "") {
                            if (isset($row->documento_origen->cabecera->pedido)) { 
                                    $referencia .= " <b>PED:</b> ".$row->documento_origen->cabecera->pedido->pedido;
                                if ($row->documento_origen->cabecera->pedido->proveedor) {
                                    $referencia .= " <b>PROV:</b> ".$row->documento_origen->cabecera->pedido->proveedor->nombrecomercial;
                                }
                            }
                        }
                        //  REFERENCIA EG PACIENTES
                        if (isset($row->documento_origen->cabecera)) {
                            $hcp = null; $paciente = null; $recibo_cobro = null; $factura=null;
                            if ($row->documento_origen->cabecera->id_hc_procedimientos) {
                            $hcp = \Sis_medico\hc_procedimientos::find($row->documento_origen->cabecera->id_hc_procedimientos);
                            // dd($hcp);
                            $hc = $hcp->historia;
                            $agenda = $hc->agenda;
                            $paciente = $agenda->paciente; 
                            if (isset($agenda->id)) {
                              $recibo_cobro = \Sis_medico\Ct_Orden_Venta::where('id_agenda',$agenda->id)->where('estado',1)->first();
                              if (isset($recibo_cobro->id)) {
                                $factura = \Sis_medico\Ct_ventas::where('orden_venta',$recibo_cobro->id)->first();
                              }
                            }
                            }  
                            if(!is_null($hcp) and isset($hcp->hc_procedimiento_final->procedimiento)) { $referencia .="<b>PROC</b>: ".$hcp->hc_procedimiento_final->procedimiento->nombre."<br>";}
                            if(!is_null($paciente)) { $referencia .=" <b>PAC:</b> ".$paciente->nombre1 ." ". $paciente->apellido1 ." ". $paciente->apellido2 ."<br>" ;}
                            if(!is_null($factura)) { $referencia .=" <b>FACT:</b> ". $factura->nro_comprobante ."<br>" ;}
                            if(!is_null($recibo_cobro)) { $referencia .=" <b>SEG:</b> ".$recibo_cobro->seguro->nombre ;}
                        }

                        $det['descripcion']     = $row->descripcion;
                        $det['referencia']      = $referencia;
                        $det['tipo']            = $row->tipo;
                        $det['bodega']          = (isset($row->bodega->nombre))?$row->bodega->nombre:"";
                        $det['fecha']           = $row->fecha;
                        $det['cantidad']        = $row->cantidad;
                        $det['valor_unitario']  = $row->valor_unitario;
                        $det['total']           = $row->total;
                        $detalles[]             = $det;
                    }
                    $karx['detales'] = $detalles;
                    if (!empty($detalles)){ 
                        $skardex[] = $karx;
                    }
                }
            }
            // dd($skardex);
        //}

        
        
        $id_empresa     = Session::get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $productos      = Ct_productos::orderby('id', 'desc')->get();
        $bodegas        = Bodega::all();
        return view('contable/kardex_contable/table', [
            'productos' => $productos, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'getAnterior' => '[]', 'fecha_hasta' => $fecha_hasta, 'kardex' => $kardex, 'id_producto' => $id_producto, 
            'detalles' => "", 'bodegas'=>$bodegas, 'id_bodega' => $id_bodega, 'prod'=>$prod, 'skardex' => $skardex
        ]);

    }

    public function __show(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa     = Session::get('id_empresa');
        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $rfecha_desde           = $request['fecha_desde'];
            $request['fecha_desde'] = str_replace('/', '-', $request['fecha_desde']);
            $timestamp              = \Carbon\Carbon::parse($request['fecha_desde'])->timestamp;
            $fecha_desde            = date('Y-m-d', $timestamp);

            $rfecha_hasta           = $request['fecha_hasta'];
            $request['fecha_hasta'] = str_replace('/', '-', $request['fecha_hasta']);
            $timestamp              = \Carbon\Carbon::parse($request['fecha_hasta'])->timestamp;
            $fecha_hasta            = date('Y-m-d', $timestamp);
        } else {
            $fecha_desde = date('Y/m/d');
            $fecha_hasta = date('Y/m/d');
        }
        $id_producto = 0;
        if (isset($request['id_producto'])) {
            $id_producto = $request['id_producto'];
        }
        $id_bodega = 0;
        if (isset($request['id_bodega'])) {
            $id_bodega = $request['id_bodega'];
        }
        $kardex = array(); $prod = array();
        if (isset($request['id_producto']) and $request['id_producto'] != "") {
            $productos = Ct_productos_insumos::where('id_producto', $request['id_producto'])->get();
            $wproductos = array();
            foreach ($productos as $value) {
                array_push($wproductos, $value->id_insumo);
            } 
            if (!empty($wproductos)) {
                $kardex = InvKardex::whereIn('id_producto', $wproductos)
                        // ->where('id_empresa', $id_empresa)
                        ->where('estado', '!=', '2')
                        // ->where('id_empresa', Session::get('id_empresa'))
                        ->where('fecha','>=' , $fecha_desde . " 00:00:00")
                        ->where('fecha','<=' , $fecha_hasta . " 23:59:59");
                        if (isset($request['id_bodega']) and $request['id_bodega'] != "") {
                            $kardex = $kardex->where('id_bodega', $request['id_bodega']);
                        }
                        $kardex = $kardex->orderBy('fecha', 'ASC')
                                        ->orderBy('id', 'ASC')
                                        ->get();
                        //  dd($kardex);
            }
            $prod = Ct_productos::where('id', $request['id_producto'])
                                ->where('id_empresa', Session::get('id_empresa'))
                                ->first(); 
            
        }
        
        $id_empresa     = Session::get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $productos      = Ct_productos::orderby('id', 'desc')->get();
        $bodegas    = Bodega::all();
        return view('contable/kardex_contable/show', [
            'productos' => $productos, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'getAnterior' => '[]', 'fecha_hasta' => $fecha_hasta, 'kardex' => $kardex, 'id_producto' => $id_producto, 
            'detalles' => "", 'bodegas'=>$bodegas, 'id_bodega' => $id_bodega, 'prod'=>$prod
        ]);

    }
    
}