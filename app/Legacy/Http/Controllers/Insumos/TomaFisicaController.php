<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Inventario\InvProcesosController; 
use Session;
use Excel;
use Response;
use Sis_medico\Producto; 
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvKardex;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvTrasladosBodegas;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\InvCabTomaFisica;
use Sis_medico\InvDetTomaFisica;
use Sis_medico\InvDetTomaFisicaLog;
use Sis_medico\Movimiento;
use Sis_medico\InvCosto;


class TomaFisicaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->noprocesados = array();
    }   

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22, 7)) == false) {
            return true;
        }
    }

    public function index()
    {
        # code...
    }

    public function proceso($file=null,$id_bodega)
    {   //dd("proceso ".$file); 
        $x = 1;
        $serie = 0;
        $resp['status'] = '';
        $resp['mensaje'] = '';
        $tipo_mov = "";
        $noprocesados = array();
        if ($file==null) {
            echo "NO EXISTE EL ARCHIVO ";
        }
        $data = array();
        DB::beginTransaction();
        Session::put('documentoEgreso', 0);
        Session::put('marca', "");
        Session::put('id_toma', 0);
        echo "INICIO DE PROCESO <br>";
        try {
            $ip_cliente                         = $_SERVER["REMOTE_ADDR"];
            $idusuario                          = Auth::user()->id;
            // CABECERA DE LA TOMAFISICA
            $cab_inv_tom = InvCabTomaFisica::where('fecha',date('Y-m-d'))
                                            ->where('id_bodega',$id_bodega)
                                            ->first();
            if (!isset($cab_inv_tom->id)) {
                $cab_inv_tom                        = new InvCabTomaFisica;
                $cab_inv_tom->observacion           = "TOMAFISICA A FECHA ".date('d/m/Y');
                $cab_inv_tom->fecha                 = date('Y-m-d');
                $cab_inv_tom->id_empresa            = Session::get('id_empresa'); 
                $cab_inv_tom->id_bodega             = $id_bodega;
                $cab_inv_tom->ip_creacion           = $ip_cliente;
                $cab_inv_tom->ip_modificacion       = $ip_cliente;
                $cab_inv_tom->id_usuariocrea        = $idusuario;
                $cab_inv_tom->id_usuariomod         = $idusuario;
                $cab_inv_tom->save();
            }

            InvDetTomaFisicaLog::truncate();

            
            $id_toma = $cab_inv_tom->id;
            Session::put('id_toma', $id_toma);
            // $file = "TOMAF//".$file;

            if ($id_bodega==1) {
                $file = "TOMAF//BODEGA1//".$file;
            } else {
                $file = "TOMAF//BODEGA2//".$file;
            }

            Excel::filter('chunk')->load($file . '.xlsx')->chunk(600, function ($reader) use ($x, $serie, $noprocesados, $data, $id_toma) {
                $this->exc($reader, $id_toma);
            });
            // procesa los inventarios no registrados en el archivo 
            $this->egresosInventario($cab_inv_tom->id);
            DB::commit();
            // DB::rollBack();
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
        echo "FIN DE PROCESO <br>";
        echo "<pre>"; print_r($data); echo "</pre>";
    }

    public function generar_serie()
    {
        $rand = rand(1,999);
        $serie = null;
        if ($rand != null) {

            $serie = date('YmdHis') . $rand;
        } else {
            $serie = date('YmdHis') . $rand;
        }
        return $serie; 
    }

    public function exc($reader,$id_toma)
    {   
        echo "EXC <br>";
        $x = 1;
        $documento = array('I'=>0, 'E'=>0);
        Session::put('documentoEgreso', 0);
        foreach ($reader as $book) {  
            echo "----------------------------------------------------------------------------------------- <br>";
            //dd($book);
            $inv_serie = null;
            $producto = null;
            # VALIDO LOS CAMPOS #
            $data = (object)$book;
            // FECHA DE VENCIMIENTO //
            if (isset($data->fecha_exp) and ($data->fecha_exp != "" || !is_null($data->fecha_exp) || strtotime($data->fecha_exp) == false)) {
                $unix_date = ($data->fecha_exp - 25569) * 86400;
                $excel_date = 25569 + ($unix_date / 86400);
                $unix_date = ($excel_date - 25569) * 86400;
                $fecha_exp = gmdate("Y-m-d", intval($unix_date));
                $data->fecha_exp = $fecha_exp;
            }else if(isset($data->fecha_exp) and $data->fecha_exp =="" || is_null($data->fecha_exp)  || $data->fecha_exp==' ' || is_null($data->fecha_exp)) {
                $fecha_exp = "2022-12-31";
                $data->fecha_exp = $fecha_exp;
            }
            if ($data->fecha_exp<date('Y-m-d')){
                $data->fecha_exp = '2024-12-31';
            }
            // 
            if (isset($book->referencia) and isset($book->serie) and isset($book->bodega) ) {
                if ($book->serie==0) {
                    $book->serie = $this->generar_serie();
                    echo "NUEVA SERIE ".$book->serie."<br>";
                }
                # BUSCO LA SERIE O REFERENCIA // SI NO EXISTE LA REFERENCIA SE GUARDA PARA PRSENTAR UN REPORTE AL FINAL DE PROCESO
                $book->serie = str_replace('-','',$book->serie);
                $inv_serie = InvInventarioSerie::where('serie', $book->serie)
                                    ->where('id_bodega', $book->bodega)
                                    ->where('estado', 1)
                                    ->first();
                if (!isset($inv_serie->id)){
                    if (isset($book->referencia) and $book->referencia!= null) {
                        $producto = Producto::where('codigo',$book->referencia)
                                    ->where('estado', 1)
                                    ->first();
                        if (!isset($producto->id)) {
                            $movimiento = Movimiento::where('serie', $book->serie)->first();
                            if (isset($movimiento->id_producto)) {
                                $producto   = Producto::find($movimiento->id_producto); 
                            }

                        }
                        /* if (isset($producto->id)){
                            $inv_serie = InvInventarioSerie::where('id_producto', $producto->id)
                                    ->where('id_bodega', $book->bodega)
                                    ->where('estado', 1)
                                    ->first();
                        } else {
                            echo "No existe el producto: ".$book->referencia."<br>";
                            $noprocesados[] = array('codigo'=>$book->referencia, 'serie'=>$book->serie, 'msj' => 'No existe el producto');
                            //break;
                        } */
                    }

                }
                if (isset($producto->id)) {
                    $data->producto     = $producto;
                } else {
                    $producto = Producto::where('codigo', $data->referencia)->first();
                    if (isset($prod->id)) {
                        $data->producto     = $producto;
                    }
                }
                if (isset($inv_serie->id)) {
                    $data->inv_serie    = $inv_serie;
                } 
                if (isset($inv_serie->id)) {
                    $data->inv_serie    = $inv_serie;
                    # CALCULO LA DIFERENCIA
                    $diferencia = $inv_serie->existencia - $book->cantidad; 
                    echo " calculando diferencia serie $book->serie :  $inv_serie->existencia - $book->cantidad = ".$diferencia." <br>";
                    if ($diferencia>0) { # SI LA DIFERENCIA ES (+) SE REALIZA UN EGRESO
                        $data->id_toma = $id_toma; $data->tipo = 'EGR'; $data->mensaje = "SE REALIZA EGRESO DE ".$diferencia;
                        $documento = $this->movimiento($documento,$data,'E',$diferencia);
                        echo "Egreso: ".$book->referencia." ".$data->mensaje."  <br>"; 
                        //$this->log($data);
                    } elseif($diferencia<0) { # SI LA DIFERENCIA ES (-) SE REALIZA UN INGRESO 
                        $data->id_toma = $id_toma; $data->tipo = 'ING'; $data->mensaje = "SE REALIZA INGRESO DE ".$diferencia;
                        $documento = $this->movimiento($documento,$data,'I',abs($diferencia));
                        echo "Ingreso: ".$book->referencia." ".$data->mensaje." <br>";
                        //$this->log($data); 
                    }else{
                        $data->id_toma = $id_toma; $data->tipo = 'NA'; $data->mensaje = "DIFERENCIA ".$diferencia;
                        $this->log($data);
                        echo "N/A: ".$book->referencia." || ".$book->serie." || ".$data->mensaje." <br>";
                    }
                } else {
                    // REALIZO EL INGRESO //
                    if (isset($producto->id)) {
                        //$noprocesados[] = array('codigo'=>$book->referencia, 'serie'=>$book->serie, 'msj' => 'No existe el inventario');
                        echo "No existe el inventario: ".$book->referencia.", ingreso nuevo SERIE: ".$book->serie." <br>";
                        $data->id_toma = $id_toma; $data->tipo = 'INGINV'; $data->mensaje = "No existe el inventario: ".$book->referencia.", ingreso nuevo, serie: ".$book->serie;
                        $documento = $this->movimiento($documento,$data,'I',$book->cantidad);
                        //$this->log($data);
                    }
                } 
            
            } else {
                $resp['status']     = 'error';
                $resp['mensaje']    = 'FormatO no valido '.$book->referencia.' SERIE:'.$book->serie.' bodega:'.$book->bodega;
               // $noprocesados[]     = array('codigo'=>'error', 'serie'=>$book->referencia, 'msj' => 'Formato no valido');
                echo 'Formato no valido RF: '.$book->referencia.' SERIE:'.$book->serie.' bodega:'.$book->bodega."<br>";
                $data->id_toma = $id_toma; $data->tipo = 'INGINV'; $data->mensaje = 'Formato no valido referencia: '.$book->referencia.'  serie:'.$book->serie.' bodega:'.$book->bodega;
                $this->log($data);
            } 
            echo "----------------------------------------------------------------------------------------- <br>";
        }
        # CONTABILIZACION DE INGRESOS Y EGRESOS 
        # $id_asiento = InvContableCab::setAsientoContablePedido($documento['I']);
 
    }

    public function movimiento($iddocumento,$data,$tipo,$cantidad,$ing="")
    {   
        /*if (isset($data->producto->id)) {
            echo "<pre>"; print_r($data->producto->nombre); echo "</pre>";
        }*/ echo "MOVIMIENTO <br>";
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $observacion = ($tipo=='I')?'INGRESO':'EGRESO'; 
        # creo la cabecera del traslado # 
        if ($iddocumento[$tipo]==0) {
            # creo la cabecera del traslado #
                
            Session::put('marca', $data->marca);
            if($tipo=='I') {
                $m = 'ITF';
            } else {
                $m = 'ETF';
            }
            $documento = InvDocumentosBodegas::where('abreviatura_documento', $m)->first();
            $secuencia = InvDocumentosBodegas::getSecueciaTipoDocum($data->bodega, $m);  
            if ($secuencia!=0) { 
                $marca = Session::get('marca');
                $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                                                        ->where('id_bodega', $data->bodega)
                                                        ->first();

                $cab_mov_inv                        = new InvCabMovimientos;
                $cab_mov_inv->id_documento_bodega   = $documento->id;
                $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
                $cab_mov_inv->id_bodega_origen      = $data->bodega;
                $cab_mov_inv->id_bodega_destino     = $data->bodega;
                $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT); 
                $cab_mov_inv->fecha                 = date('Y-m-d');
                $cab_mov_inv->observacion           = 'TOMA_FISICA_'.$observacion.'_'.date('Ymd')."_".$marca; 
                $cab_mov_inv->id_empresa            = Session::get('id_empresa');
                $cab_mov_inv->ip_creacion           = $ip_cliente;
                $cab_mov_inv->ip_modificacion       = $ip_cliente;
                $cab_mov_inv->id_usuariocrea        = $idusuario;
                $cab_mov_inv->id_usuariomod         = $idusuario;
                $cab_mov_inv->save();

                if ($tipo=='E') {
                    Session::put('documentoEgreso', $cab_mov_inv->id);
                }
                
            }
            if($tipo=='I' and $ing!="")  { 
                $pedido     = Pedido::where('id_proveedor','0992704152001')
                            // ->where('fecha', date('Y-m-d'))
                            ->where('factura','ITF'.date(Ymd))
                            ->where('pedido','ITF'.date(Ymd))
                            ->first();
                
                if (is_null($pedido)) {
                    $input    = [
                        'id_proveedor'    => '0992704152001',
                        'pedido'          => 'ITF'.date(Ymd),
                        'tipo'            => 2,
                        'factura'         => 'ITF'.date(Ymd),
                        'fecha'           => date('Y-m-d'), 
                        'id_bodega'       => $data->bodega,
                        'id_empresa'      => '0992704152001',
                        'observaciones'   => 'INGRESO POR TOMAFISICA', 
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'created_at'      => date('Y-m-d H:i:s'),
                        'updated_at'      => date('Y-m-d H:i:s'),
                    ]; 
                    $id_pedido      = Pedido::insertGetId($input);
                    $pedido         = Pedido::find($id_pedido);
                } 
            }
        } else {
            $cab_mov_inv = InvCabMovimientos::find($iddocumento[$tipo]);
        }
            
        $cant_uso = 0;
        $iva = 0;
        if (isset($data->producto->id)) {
            if ($data->producto->iva==1) {
                $conf = Ct_Configuraciones::find(3);
                $iva  = ($cantidad * $data->precio) * $conf->iva;
            }
        }
        $id_inventario      = "";
        if (isset($data->inv_serie->id_inv_inventario)) { 
            $serie              = $data->inv_serie->serie;
            $lote               = $data->inv_serie->lote;
            $fecha_vence        = $data->inv_serie->fecha_vence;
            $inventario         = $data->inv_serie->inventario;
        } else {
            $t = ($data->consignia=='CONSIGNA')?'C':'F';
            $inventario         = InvInventario::getInventario($data->producto->id, $data->bodega, $t);
            if ($inventario!=null and $data->bodega!=null and $data->bodega!="") {
                $inventario     = InvInventario::setNeoInventario($data->producto->id, $data->bodega, $t, 0, 0);
                $serie          = $data->serie;
                $lote           = $data->lote;
                $fecha_vence    = $data->fecha_exp;
            }
        } 
        if (isset($inventario->id) and $cantidad>0) {
            if ($inventario->producto->usos!=null) {
                $cant_uso = $inventario->producto->usos;
            }
            if ($cant_uso == null or $cant_uso < 0) {
                $cant_uso = 0;
            }
            
            echo "INGRESO DETALLES <br>";
            // dd($cab_mov_inv);
            $det_mov_inv                            = new InvDetMovimientos;
            $det_mov_inv->id_inv_cab_movimientos    = $cab_mov_inv->id;
            $det_mov_inv->id_producto               = $inventario->id_producto; 
            $det_mov_inv->serie                     = $serie; 
            $det_mov_inv->lote                      = $lote; 
            $det_mov_inv->fecha_vence               = $fecha_vence; 
            $det_mov_inv->id_inv_inventario         = $inventario->id;
            $det_mov_inv->cantidad                  = $cantidad;
            $det_mov_inv->cant_uso                  = $cant_uso;
            $det_mov_inv->valor_unitario            = $data->precio;
            $det_mov_inv->subtotal                  = $cantidad*$data->precio;
            $det_mov_inv->descuento                 = 0;
            $det_mov_inv->iva                       = $iva;
            $det_mov_inv->total                     = $cantidad*$data->precio+$iva;
            $det_mov_inv->motivo                    = 'TOMA FISICA '.$observacion.' A FECHA '.date('d/m/Y'); 
            $det_mov_inv->ip_creacion               = $ip_cliente;
            $det_mov_inv->ip_modificacion           = $ip_cliente;
            $det_mov_inv->id_usuariocrea            = $idusuario;
            $det_mov_inv->id_usuariomod             = $idusuario;
            $det_mov_inv->save();

            if($tipo=='I' and $ing!="")  { 
                $input_d = [
                    'id_producto'       => $data->producto->id,
                    'cantidad'          => $cantidad,
                    'id_encargado'      => $idusuario,
                    'serie'             => $serie,
                    'id_bodega'         => $data->bodega,
                    'tipo'              => '1',
                    'fecha_vencimiento' => date('Y-m-d', strtotime($fecha_vence)),
                    'lote'              => $lote,
                    'usos'              => $cantidad * $cant_uso,
                    'descuentop'        => 0,
                    'descuento'         => 0,
                    'precio'            => $data->precio,
                    'id_pedido'         => $pedido->id,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                ];
            $id_movimiento = DB::table('movimiento')->insertGetId($input_d);
            // detalles tomafisica
            $det_inv_tom                            = new InvDetTomaFisica;
            $det_inv_tom->id_inv_cab_tomafisica     = $data->id_toma;
            $det_inv_tom->id_inventario             = $inventario->id; 
            $det_inv_tom->id_inventario_serie       = $data->inv_serie->id;
            $det_inv_tom->id_producto               = $inventario->id_producto; 
            $det_inv_tom->tipo                      = $tipo; 
            $det_inv_tom->serie                     = $serie; 
            $det_inv_tom->fecha_vence               = $fecha_vence; 
            $det_inv_tom->lote                      = $lote; 
            $det_inv_tom->precio                    = $data->precio;
            $det_inv_tom->save(); 
            echo "FIN DETALLES <br>";

            $data->tipo = 'INGN'; $data->mensaje = "INGRESO ".$data->referencia.'  serie:'.$data->serie.' bodega:'.$data->bodega;
            $this->log($data);
            } else {
                $data->tipo = 'EGRN'; $data->mensaje = "( ".$observacion." )".$data->referencia.'  serie:'.$data->serie.' bodega:'.$data->bodega;
                $this->log($data);
            }
        }
        
        InvCabMovimientos::calcularTotalCabMovimiento($cab_mov_inv->id);
        // MOVIMIENTO EN KARDEX
        $kardex = InvKardex::setKardex($cab_mov_inv->id);
        // PRECIOS
        InvProcesosController::actualizaPrecio($data);
        $iddocumento[$tipo] = $cab_mov_inv->id;
        // 
        return $iddocumento;
    }

    public function egresosInventario($id_toma)
    {
        echo "EGRESOS DEL RESTANTE DE INVENTARIO <br>";
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = Auth::user()->id;
        $id_cab_mov_inv_eg = Session::get('documentoEgreso');
        // dd("ID DEL DOCUMENTO EGRESO ".$id_cab_mov_inv);

        if ($id_cab_mov_inv_eg==0) {
            $tomaf = InvCabTomaFisica::find($id_toma);
            if (isset($tomaf->id)) {
                $m = 'ETF'; 
                $documento = InvDocumentosBodegas::where('abreviatura_documento', $m)->first();
                $secuencia = InvDocumentosBodegas::getSecueciaTipoDocum($tomaf->id_bodega, $m);  
                if ($secuencia!=0) { 
                    $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                                                            ->where('id_bodega', $tomaf->id_bodega)
                                                            ->first();
    
                    $cab_mov_inv_eg                        = new InvCabMovimientos;
                    $cab_mov_inv_eg->id_documento_bodega   = $documento->id;
                    $cab_mov_inv_eg->id_transaccion_bodega = $transaccion->id;
                    $cab_mov_inv_eg->id_bodega_origen      = $tomaf->id_bodega;
                    $cab_mov_inv_eg->id_bodega_destino     = $tomaf->id_bodega;
                    $cab_mov_inv_eg->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT); 
                    $cab_mov_inv_eg->observacion           = 'TOMA FISICA EGRESO A FECHA '.date('d/m/Y');
                    $cab_mov_inv_eg->fecha                 = date('Y-m-d');
                    $cab_mov_inv_eg->observacion           = 'TOMA FISICA EGRESO '.str_pad($secuencia, 9, "0", STR_PAD_LEFT); 
                    $cab_mov_inv_eg->id_empresa            = Session::get('id_empresa');
                    $cab_mov_inv_eg->ip_creacion           = $ip_cliente;
                    $cab_mov_inv_eg->ip_modificacion       = $ip_cliente;
                    $cab_mov_inv_eg->id_usuariocrea        = $idusuario;
                    $cab_mov_inv_eg->id_usuariomod         = $idusuario;
                    $cab_mov_inv_eg->save();
     
                    Session::put('documentoEgreso', $cab_mov_inv_eg->id);
                    $id_cab_mov_inv_eg = $cab_mov_inv_eg->id;
                    
                }

            }
        }

        if ($id_cab_mov_inv_eg!=0) {

            $sql = "select id_producto, id_bodega
                    from inv_det_tomafisica_log 
                    where id_inventario is not null 
                    group by id_producto, id_bodega
                    and id_inv_cab_tomafisica = $id_toma
                    ";
            $movimientos = DB::select(DB::raw($sql));  
            foreach ($movimientos as $value) {
                // echo "+++++++++++++++++++++++         +++++++++++++++++++++++ <br>";
                // echo "+++++++++++++++++++++++ EGRESOS +++++++++++++++++++++++ <br>";
                // echo "+++++++++++++++++++++++         +++++++++++++++++++++++ <br>";
                if ($value->id_producto!=null and $value->id_bodega!=null) {
                    $sql2 = " select coalesce(id,0) as id from inv_inventario_serie
                        where serie not in ( select serie from inv_det_tomafisica_log
                            where id_producto = ".$value->id_producto."
                            and id_bodega = ".$value->id_bodega."
                            and id_inv_cab_tomafisica = $id_toma )
                        and id_producto = ".$value->id_producto."
                        and id_bodega = ".$value->id_bodega;
                    // echo "<br>";
                    // echo $sql2;
                    // echo "<br>";
                    $series = DB::select(DB::raw($sql2));   
                    echo $sql2;  echo "<br>";
                    foreach ($series as $row) {
                        if (isset($row->id)) {
                            $serie = InvInventarioSerie::find($row->id);   
                            // dd($serie);
                            if ($serie->existencia>0) {
                                $iva = 0; $costop = 0;
                                $subt = $serie->existencia * $costop;
                                // INV COSTO //
                                $costo = InvCosto::where('id_producto', $serie->producto->id)->first();
                                if (isset($costo->id)) {
                                    $costop = $costo->costo_promedio;
                                }
                                if (isset($serie->producto->id) and $serie->producto->iva==1) {
                                    $conf = Ct_Configuraciones::find(3);
                                    $iva  = $subt * $conf->iva;
                                }
                                $total = $subt + $iva;
                                // EGRESO //
                                echo "EGRESO ID: ".$row->id." | BODEGA: ".$value->id_bodega." | PRODUCTO: ". $serie->producto->nombre." | SERIE: ".$serie->serie." | cantidad: ".$serie->existencia." <br>";
                                $det_mov_inv_eg                            = new InvDetMovimientos;
                                $det_mov_inv_eg->id_inv_cab_movimientos    = $id_cab_mov_inv_eg;
                                $det_mov_inv_eg->id_producto               = $serie->id_producto; 
                                $det_mov_inv_eg->serie                     = $serie->serie; 
                                $det_mov_inv_eg->lote                      = $serie->lote; 
                                $det_mov_inv_eg->fecha_vence               = $serie->fecha_vence; 
                                $det_mov_inv_eg->id_inv_inventario         = $serie->id_inv_inventario;
                                $det_mov_inv_eg->cantidad                  = $serie->existencia;
                                $det_mov_inv_eg->cant_uso                  = $serie->existencia_uso;
                                $det_mov_inv_eg->valor_unitario            = $costop;
                                $det_mov_inv_eg->subtotal                  = $subt;
                                $det_mov_inv_eg->descuento                 = 0;
                                $det_mov_inv_eg->iva                       = $iva;
                                $det_mov_inv_eg->total                     = $total;
                                $det_mov_inv_eg->kardex                    = 0;
                                $det_mov_inv_eg->motivo                    = 'TOMA FISICA EGRESO A FECHA '.date('dmY'); 
                                $det_mov_inv_eg->ip_creacion               = $ip_cliente;
                                $det_mov_inv_eg->ip_modificacion           = $ip_cliente;
                                $det_mov_inv_eg->id_usuariocrea            = $idusuario;
                                $det_mov_inv_eg->id_usuariomod             = $idusuario;
                                $det_mov_inv_eg->save();
                            }
                            $serie->existencia = 0;
                            $serie->existencia_uso = 0;
                            $serie->save();
                        }
                    } 
                }
                
    
            }
            // MOVIMIENTO EN KARDEX
            InvKardex::setKardex($id_cab_mov_inv_eg);
            InvCabMovimientos::calcularTotalCabMovimiento($id_cab_mov_inv_eg);

        }
    }

    public function log($data)
    {
        
        echo "LOG <br>";
        $ip_cliente                         = $_SERVER["REMOTE_ADDR"];
        $idusuario                          = Auth::user()->id; 
        $log                                = new InvDetTomaFisicaLog;
        $log->id_inv_cab_tomafisica         = Session::get('id_toma');
        if (isset($data->inv_serie->id_inv_inventario)){
            $log->id_inventario             = $data->inv_serie->id_inv_inventario;    
        }
        if (isset($data->inv_serie->id)){
            $log->id_inventario_serie       = $data->inv_serie->id;   
            $log->id_producto               = $data->inv_serie->id_producto;   
        }

        $prod = Producto::where('codigo', $data->referencia)->first();
        if (isset($prod->id)) {
            $log->id_producto = $prod->id;
        } else {
            return;
        }

        $log->serie                         = $data->serie;
        $log->fecha_vence                   = $data->fecha_exp;
        $log->lote                          = $data->lote;
        $log->precio                        = $data->precio;
        $log->id_bodega                     = $data->bodega;
        $log->tipo                          = $data->tipo;
        $log->mensaje                       = $data->mensaje; 
        $log->save();
        echo 'Registro en log  '."<br>";
    }
    
}


