<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Bodega;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Marca;
use Sis_medico\Movimiento;
use Sis_medico\Producto;
use Sis_medico\Proforma_Cabecera;
use Sis_medico\Proforma_Detalle;
use Sis_medico\Proforma_Venta_Cab;
use Sis_medico\Proveedor;
use Sis_medico\Tipo;
use Sis_medico\User;
use PHPExcel_Style_NumberFormat;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvKardex;
use Sis_medico\Movimiento_Paciente;
use Sis_medico\Paciente;
use Sis_medico\Pedido;
use Sis_medico\hc_procedimientos;
use Sis_medico\Historiaclinica;
use Sis_medico\Agenda;
use Sis_medico\InvDetMovimientos;
use Sis_medico\Empresa;
use Illuminate\Support\Facades\Session;
use Sis_medico\Ct_Kardex;
use Sis_medico\Form_Preadmision_Grupo;
use Sis_medico\Http\Requests\Request as RequestsRequest;
use Sis_medico\Planilla;
use Sis_medico\Planilla_Cabecera;
use Sis_medico\Planilla_Detalle;



class ReportesController extends Controller
{
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 3, 7, 20, 10, 5)) == false) {
            return true;
        }
    }

    public function reporte_bodega(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa  = $request->session()->get('id_empresa');
        $nombres = $request['nombres'];
        $bodega= $request['bodega'];
        $productos = DB::table('inv_det_movimientos as m')
            ->leftjoin('producto as p1', 'p1.id', 'm.id_producto')
            ->leftjoin('inv_inventario as inv', 'inv.id', 'm.id_inv_inventario')
            ->leftjoin('bodega as b', 'b.id', 'inv.id_bodega')
            ->leftjoin('pedido as p', 'p.id', 'm.id_pedido')
            ->leftjoin('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->leftjoin('tipo as tip', 'tip.id', 'p1.tipo_producto')
            ->join('marca as marc', 'marc.id', 'p1.id_marca')
            ->where('p.id_empresa', $id_empresa)
            ->whereNull('p.deleted_at')
            ->where('m.estado', '1')
            //->where('m.tipo', '1')
            //->whereNull('ped.consecion')
            //->where('ped.tipo','<>','3')
            //->groupBy('m.serie')
            ->select( 'p1.nombre as nombre_producto', 'p1.codigo', 'p1.descripcion', 'b.nombre as nombre_bodega', 'marc.nombre as nombre_marca', 'm.cantidad' ,'m.updated_at as fecha', 'm.fecha_vence', 'm.serie', 'p.pedido', 'prov.nombrecomercial', 'tip.nombre as nombre_tipo','m.lote');

        if ($nombres != null) {
            $productos = $productos->where('p1.nombre', 'like', '%' . $nombres . '%');
        }
        if($request['codigo']!=null){
            $productos = $productos->where('p1.codigo', 'like', '%' . $request['codigo'] . '%');
        }
        if($bodega!=null){
            $productos= $productos->where('inv.id_bodega',$bodega);
        }
        $bodegas= Bodega::where('estado','1')->where('id_empresa', Session::get('id_empresa'))->get();
        $productos = $productos->paginate(15);

        return view('insumos/reporte/bodega/index', ['productos' => $productos,'bodegas'=>$bodegas,'codigo'=>$request['codigo'],'bodega'=>$bodega, 'nombres' => $nombres]);
    }

    public function reporte_producto_bodega(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        
        
        $id_empresa  = $request->session()->get('id_empresa');
        $nombres = $request['nombres'];
        $bodega= $request['bodega'];
        $productos = DB::table('inv_det_movimientos as m')
            ->leftjoin('producto as p1', 'p1.id', 'm.id_producto')
            ->leftjoin('inv_inventario as inv', 'inv.id', 'm.id_inv_inventario')
            ->leftjoin('bodega as b', 'b.id', 'inv.id_bodega')
            ->leftjoin('pedido as p', 'p.id', 'm.id_pedido')
            ->leftjoin('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->leftjoin('tipo as tip', 'tip.id', 'p1.tipo_producto')
            ->join('marca as marc', 'marc.id', 'p1.id_marca')
            ->where('p.id_empresa', $id_empresa)
            ->where('m.estado', '1')
            ->whereNull('p.deleted_at')
            //->where('m.tipo', '1')
            //->whereNull('ped.consecion')
            //->where('ped.tipo','<>','3')
            //->groupBy('m.serie')
            ->select( 'p1.nombre as nombre_producto', 'p1.codigo', 'p1.descripcion', 'b.nombre as nombre_bodega', 'marc.nombre as nombre_marca', 'm.cantidad' ,'m.updated_at as fecha', 'm.fecha_vence', 'm.serie', 'p.pedido', 'prov.nombrecomercial', 'tip.nombre as nombre_tipo','m.lote');

        if ($nombres != null) {
            $productos = $productos->where('p1.nombre', 'like', '%' . $nombres . '%');
        }
        if($request['codigo']!=null){
            $productos = $productos->where('p1.codigo', 'like', '%' . $request['codigo'] . '%');
        }
        if($bodega!=null){
            $productos= $productos->where('inv.id_bodega','like', '%' . $bodega . '%');
        }
        $productos = $productos->get();

        $fecha_d = date('Y/m/d');

        Excel::create('Reporte_Existentes_Bodega'. $fecha_d , function ($excel) use ($productos){

          $excel->sheet('Reporte Existentes en Bodega', function($sheet) use($productos){
          $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");

          $fecha_d = date('Y/m/d');
           $sheet->mergeCells('A1:M2');
                     $sheet->cell('A1', function ($cell){
                         $cell->setValue('Reporte de Productos Existentes en Bodega');
                         $cell->setFontColor('#010101');
                         $cell->setBackground('#D1E9F2');
                         $cell->setFontWeight('bold');
                         $cell->setFontSize('20');
                         $cell->setAlignment('center');
                         $cell->setBorder('thin', 'thin', 'thin', 'thin');
                     });
           $i = 3;

          $arrTitulos= ["Codigo", "Nombre", "Descripción", "Serie", "Pedidos", "Bodega" ,"Proveedores", "Marca", "Tipo de Producto", "Cantidad en Bodega", "Lote", "Fecha", "Fecha de Vencimiento"];

          $comienzo = 3;

          for ($i=0; $i < count($arrTitulos) ; $i++) {
             $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use($arrTitulos, $i) {
                     // manipulate the cel
                     $cell->setValue($arrTitulos[$i]);
                     $cell->setBackground('#D1E9F2');
                     $cell->setFontWeight('bold');
                     $cell->setBorder('thin', 'thin', 'thin', 'thin');
             });
          }
          $comienzo++;
          //dd($biopsias->get());
          $fecha_hoy = date('Y/m/d');
          foreach($productos as $value){
             $arrValue = [];

             $arrValue = [$value->codigo, $value->nombre_producto, $value->descripcion,  $value->serie."-",  $value->pedido ,$value->nombre_bodega, $value->nombrecomercial, $value->nombre_marca, $value->nombre_tipo, $value->cantidad, $value->lote, $value->fecha, $value->fecha_vence];

             for ($i=0; $i < count($arrValue) ; $i++) {
                 $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use($arrValue, $i) {
                         // manipulate the cel
                         $cell->setValue($arrValue[$i]);
                         $cell->setBorder('thin', 'thin', 'thin', 'thin');
                 });
              }
              $comienzo++;
          }

        });

       })->export('xlsx');

    }

    public function buscador_master(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $productos = [];
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $codigo      = $request['codigo'];
        $nombre      = $request['nombres'];
        $bodega      = $request['bodega'];
        $tipo        = $request['tipo'];
        $marca       = $request['marca'];
        $tipo_prod   = $request['tipo_producto'];
        $proveedores = $request['proveedor'];
        $caducados =  $request['caducados'];
        $fecha_hoy     = date('Y-m-d');

        $productos = ReportesController::busqueda_master($request);
        //dd($productos[0][0]);

        $bodega_nombre = bodega::where('estado', '1')->where('id_empresa', Session::get('id_empresa'))->get();
        $marca_nombre  = marca::where('estado', '1')->get();
        $tipo_producto = tipo::where('estado', '1')->get();
        $proveedor     = proveedor::where('estado', '1')->get();
       // dd($productos);
        return view('insumos/reporte/buscador_master/index', ['productos' => $productos,'bodega'=>$bodega,'tipo'=>$tipo, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'bodega_nombre' => $bodega_nombre, 'marca_nombre' => $marca_nombre, 'nombre' => $nombre, 'codigo' => $codigo, 'marca' => $marca, 'bodega' => $bodega, 'fecha_hoy' => $fecha_hoy, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'tipo_producto' => $tipo_producto, 'tipo_prod' => $tipo_prod, 'proveedor' => $proveedor, 'proveedores' => $proveedores]);

    }

    public static function busqueda_master($request)
    {
        
        $id_empresa  = $request->session()->get('id_empresa');
        $productos1 = DB::table('inv_kardex as ik')
            ->leftjoin('inv_det_movimientos as idm', 'idm.id', 'ik.id_inv_det_movimientos')
            ->leftjoin('movimiento_paciente as mp', 'mp.id', 'idm.id_movimiento_paciente')
            ->leftjoin('bodega as b', 'b.id', 'ik.id_bodega')
            ->join('pedido as p', 'p.id', 'idm.id_pedido')
            ->leftjoin('planilla_detalle as pd', 'pd.id_movimiento_paciente','mp.id')
            ->leftjoin('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->leftjoin('producto as p1', 'p1.id', 'ik.id_producto')
            ->leftjoin('tipo as tip', 'tip.id', 'p1.tipo_producto')
            ->leftjoin('marca as m', 'm.id', 'p1.id_marca')
            ->where('p.id_empresa', $id_empresa)
            ->where('idm.cantidad', '>=', '1')
            ->whereNull('p.deleted_at')
            ->whereNotNull('pd.id_movimiento_paciente')
            //->groupby('pd.id_movimiento_paciente')
            ->select('pd.id_movimiento_paciente','p1.codigo', 'p1.nombre as nombre_producto', 'p1.descripcion', 'b.id', 'b.nombre as nombre_bodega', 'ik.cantidad', 'idm.serie', 'p.pedido', 'prov.nombrecomercial', 'm.nombre as nombre_marca', 'tip.nombre as nombre_tipop', 'idm.fecha_vence', 'ik.tipo');

        $productos2 = DB::table('inv_kardex as ik')
            ->leftjoin('inv_det_movimientos as idm', 'idm.id', 'ik.id_inv_det_movimientos')
            ->leftjoin('movimiento_paciente as mp', 'mp.id', 'idm.id_movimiento_paciente')
            ->leftjoin('bodega as b', 'b.id', 'ik.id_bodega')
            ->join('pedido as p', 'p.id', 'idm.id_pedido')
            ->leftjoin('planilla_detalle as pd', 'pd.id_movimiento_paciente','mp.id')
            ->leftjoin('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->leftjoin('producto as p1', 'p1.id', 'ik.id_producto')
            ->leftjoin('tipo as tip', 'tip.id', 'p1.tipo_producto')
            ->leftjoin('marca as m', 'm.id', 'p1.id_marca')
            ->where('p.id_empresa', $id_empresa)
            ->where('idm.cantidad', '>=', '1')
            ->whereNull('p.deleted_at')
            ->whereNull('pd.id_movimiento_paciente')
            ->select('pd.id_movimiento_paciente','p1.codigo', 'p1.nombre as nombre_producto', 'p1.descripcion', 'b.id', 'b.nombre as nombre_bodega', 'ik.cantidad', 'idm.serie', 'p.pedido', 'prov.nombrecomercial', 'm.nombre as nombre_marca', 'tip.nombre as nombre_tipop', 'idm.fecha_vence', 'ik.tipo');
       
        if (count($request->all()) > 0) {
            if (!is_null($request->codigo)) {
                $productos1 = $productos1->where('p1.codigo', 'like', '%' . $request->codigo . '%');
                $productos2 = $productos2->where('p1.codigo', 'like', '%' . $request->codigo . '%');
            }

            if (!is_null($request->nombres)) {
                $productos1 = $productos1->where('p1.nombre', 'like', '%' . $request->nombres . '%');
                $productos2 = $productos2->where('p1.nombre', 'like', '%' . $request->nombres . '%');
            }
            if (!is_null($request->bodega)) {
                $productos1 = $productos1->where('b.id', $request->bodega);
                $productos2 = $productos2->where('b.id', $request->bodega);
            }
            if (!is_null($request->tipo_producto)) {
                $productos1 = $productos1->where('tip.id', $request->tipo_producto);
                $productos2 = $productos2->where('tip.id', $request->tipo_producto);
            }
            if (!is_null($request->proveedor)) {
                $productos1 = $productos1->where('prov.id', $request->proveedor);
                $productos2 = $productos2->where('prov.id', $request->proveedor);
            }
            if (!is_null($request->marca)) {
                $productos1 = $productos1->where('m.id', $request->marca);
                $productos2 = $productos2->where('m.id', $request->marca);
            }
            if (!is_null($request->caducados)) {
                if ($request->caducados == 1) {
                    $productos1 = $productos1->whereDate('idm.fecha_vence', '<=', date('Y-m-d'));
                    $productos2 = $productos2->whereDate('idm.fecha_vence', '<=', date('Y-m-d'));
                } elseif ($request->caducados == 2) {
                    $productos1 = $productos1->whereDate('idm.fecha_vence', '>=', date('Y-m-d'));
                    $productos2 = $productos2->whereDate('idm.fecha_vence', '>=', date('Y-m-d'));
                } elseif ($request->caducados == null) {
                    $productos1 = $productos1->whereDate('idm.fecha_vence', '>=', date('Y-m-d'));
                    $productos2 = $productos2->whereDate('idm.fecha_vence', '>=', date('Y-m-d'));
                }
            }
            if (!is_null($request->tipo)) {
                $productos1 = $productos1->where("p.tipo", $request->tipo);
                $productos2 = $productos2->where("p.tipo", $request->tipo);
            }
            if (!is_null($request->fecha)) {
                $productos1  = $productos1->where('idm.fecha_vence', ">=", "{$request->fecha} 00:00:00");
                $productos2  = $productos2->where('idm.fecha_vence', ">=", "{$request->fecha} 00:00:00");
            }

            if (!is_null($request->fecha_hasta)) {
                $productos1 =  $productos1->where('idm.fecha_vence', "<=", "{$request->fecha_hasta} 23:59:59");
                $productos2 =  $productos2->where('idm.fecha_vence', "<=", "{$request->fecha_hasta} 23:59:59");
            }
            //$productos = $productos1->union($productos2)
            
        }
        //dd($productos1->union($productos2)->paginate(10));
        $productos1  = $productos1->limit(150)->get();
        //dd($productos1, "hola");
        $productos2  = $productos2->limit(150)->get();
        //$productos = $productos1->union($productos2);
        //dd($productos);
        return [$productos1,$productos2];
        //dd($productos);
    }

    public function reporte_master(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $productos = ReportesController::busqueda_master($request);

        $fecha_d = date('Y/m/d');

        Excel::create('Reporte_Master_Insumos'. $fecha_d , function ($excel) use ($productos){

          $excel->sheet('Reporte Master de Insumos', function($sheet) use($productos){
          $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");

          $fecha_d = date('Y/m/d');
           $sheet->mergeCells('A1:M2');
                     $sheet->cell('A1', function ($cell){
                         $cell->setValue('Reporte Master de Insumos Médicos');
                         $cell->setFontColor('#010101');
                         $cell->setBackground('#D1E9F2');
                         $cell->setFontWeight('bold');
                         $cell->setFontSize('20');
                         $cell->setAlignment('center');
                         $cell->setBorder('thin', 'thin', 'thin', 'thin');
                     });


          $i = 3;

          $arrTitulos= ["Codigo", "Nombre", "Descripción", "Bodega" ,"Cantidad","Tipo","Serie", "Pedidos" , "Proveedores","Marca", "Tipo de Producto","Fecha de Vencimiento", "Estado"];

          $comienzo = 3;

          for ($i=0; $i < count($arrTitulos) ; $i++) {
             $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use($arrTitulos, $i) {
                     // manipulate the cel
                     $cell->setValue($arrTitulos[$i]);
                     $cell->setBackground('#D1E9F2');
                     $cell->setFontWeight('bold');
                     $cell->setBorder('thin', 'thin', 'thin', 'thin');
             });
          }
          $comienzo++;
          //dd($biopsias->get());
          $fecha_hoy = date('Y/m/d');
          foreach($productos as $p){
                
            foreach($p as $value){
                $arrValue = [];

                $estado = [];
                if ($value->fecha_vence < date('Y-m-d')) {
                    $estado ='Producto caducado';
                } else {
                    $estado ='Producto Proximo a Caducado';
                }
                $tipo=[];
                if($value->tipo == 'I'){
                $tipo = "Ingreso";
                } else if($value->tipo == 'E'){
                $tipo = "Egreso";
                }elseif($value->tipo == 'T'){
                $tipo = "Traslado";
                }

                $arrValue = [$value->codigo, $value->nombre_producto, $value->descripcion, $value->nombre_bodega,$value->cantidad, $tipo ,$value->serie."-", $value->pedido, $value->nombrecomercial , $value->nombre_marca, $value->nombre_tipop, $value->fecha_vence, $estado];

                for ($i=0; $i < count($arrValue) ; $i++) {
                    $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use($arrValue, $i) {
                            // manipulate the cel
                            $cell->setValue($arrValue[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
            }
          }

        });


       })->export('xlsx');

    }

    public function reporte_caducado(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $nombres_prod = $request['nombres'];
        $fecha        = $request['fecha'];
        $fecha_hasta  = $request['fecha_hasta'];
        $id_empresa  = $request->session()->get('id_empresa');
        $fecha_hoy = date('Y-m-d');
        //BUSCA POR NOMBRE

        $fecha_cadu = DB::table('inv_det_movimientos as m')
        ->leftjoin('inv_kardex as ik', 'm.id', 'ik.id_inv_det_movimientos')
        ->leftjoin('inv_inventario_serie as invs','m.id_producto','invs.id_producto')
        ->leftjoin('producto as p1', 'm.id_producto', 'p1.id')
        ->leftjoin('pedido as p', 'p.id', 'm.id_pedido')
        ->leftjoin('bodega as b', 'b.id', 'p.id_bodega')
        ->leftjoin('proveedor as prov', 'prov.id', 'p.id_proveedor')
        ->leftjoin('marca as mar', 'mar.id', 'p1.id_marca')
        ->leftjoin('tipo as tip', 'tip.id', 'p1.tipo_producto')
        ->whereNull('ik.dar_baja')
        ->where('invs.existencia', '>', '1')
        ->where('p.id_empresa', $id_empresa)
            //->whereNull('ped.consecion')
           /* ->where('p.tipo','<>','3')
            ->where('m.estado', '1')
            ->where(function ($query) {
                $query->where('p1.tipo', '2')
                    ->orWhere('p1.tipo', '1');})
            ->where(function ($query) {
                $query->where('m.fecha_vence', '<', 'now()')
            ->orWhereRaw('m.fecha_vence < DATE_ADD(NOW(), INTERVAL 3 MONTH)');})*/

            ->select('p1.codigo', 'p1.nombre', 'p1.descripcion', 'p.pedido', 'm.serie', 'm.fecha_vence', 'b.nombre as nombre_bodega', 'prov.nombrecomercial', 'mar.nombre as nombre_marca', 'invs.existencia' , 'tip.nombre as nombre_tipoproducto');

        if ($nombres_prod != null) {
            //busca si el nombre del producto esta lleno
            $fecha_cadu = $fecha_cadu->where('p1.nombre', 'like', '%' . $nombres_prod . '%');

        } elseif ($fecha != null && $fecha_hasta != null) {

            $fecha_cadu = $fecha_cadu->whereBetween('m.fecha_vence', [$fecha, $fecha_hasta]);
        }

        $fecha_cadu = $fecha_cadu->paginate(15);

        return view('insumos/reporte/caducado/index', ['fecha_cadu' => $fecha_cadu, 'fecha_hoy' => $fecha_hoy, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'nombres_prod' => $nombres_prod]);
    }

    public function reporte_producto_caducado(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
         
        $id_empresa  = $request->session()->get('id_empresa');
        $nombres_prod = $request['nombres'];
        $fecha        = $request['fecha'];
        $fecha_hasta  = $request['fecha_hasta'];

        $fecha_hoy = date('Y-m-d');

        $fecha_cadu = DB::table('inv_det_movimientos as m')
            ->leftjoin('inv_kardex as ik', 'm.id', 'ik.id_inv_det_movimientos')
            ->leftjoin('inv_inventario_serie as invs','m.id_producto','invs.id_producto')
            ->leftjoin('producto as p1', 'm.id_producto', 'p1.id')
            ->leftjoin('pedido as p', 'p.id', 'm.id_pedido')
            ->leftjoin('bodega as b', 'b.id', 'p.id_bodega')
            ->leftjoin('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->leftjoin('marca as mar', 'mar.id', 'p1.id_marca')
            ->leftjoin('tipo as tip', 'tip.id', 'p1.tipo_producto')
            ->whereNull('ik.dar_baja')
            ->where('invs.existencia', '>', '1')
            ->where('p.id_empresa', $id_empresa)
            //->whereNull('ped.consecion')
           /* ->where('p.tipo','<>','3')
            ->where('m.estado', '1')
            ->where(function ($query) {
                $query->where('p1.tipo', '2')
                    ->orWhere('p1.tipo', '1');})
            ->where(function ($query) {
                $query->where('m.fecha_vence', '<', 'now()')
            ->orWhereRaw('m.fecha_vence < DATE_ADD(NOW(), INTERVAL 3 MONTH)');})*/

            ->select('p1.codigo', 'p1.nombre', 'p1.descripcion', 'p.pedido', 'm.serie', 'm.fecha_vence', 'b.nombre as nombre_bodega', 'prov.nombrecomercial', 'mar.nombre as nombre_marca', 'invs.existencia' , 'tip.nombre as nombre_tipoproducto');

        if ($nombres_prod != null) {
            //busca si el nombre del producto esta lleno
            $fecha_cadu = $fecha_cadu->where('p1.nombre', 'like', '%' . $nombres_prod . '%');

        } elseif ($fecha != null && $fecha_hasta != null) {

            $fecha_cadu = $fecha_cadu->whereBetween('m.fecha_vence', [$fecha, $fecha_hasta]);
        }

        $fecha_cadu = $fecha_cadu->get();

        Excel::create('Reporte_de_Insumos_caducados'. $fecha_hoy , function ($excel) use ($fecha_cadu){

            $excel->sheet('Reporte de Insumos caducados', function($sheet) use($fecha_cadu){
            $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");

            $fecha_hoy = date('Y/m/d');
             $sheet->mergeCells('A1:L2');
                       $sheet->cell('A1', function ($cell){
                           $cell->setValue('REPORTE DE INSUMOS CADUCADOS / CADUCAR');
                           $cell->setFontColor('#010101');
                           $cell->setBackground('#D1E9F2');
                           $cell->setFontWeight('bold');
                           $cell->setFontSize('20');
                           $cell->setAlignment('center');
                           $cell->setBorder('thin', 'thin', 'thin', 'thin');
                       });


            $i = 3;

            $arrTitulos= ["Codigo", "Nombre", "Descripción", "Serie", "Pedido", "Bodega", "Proveedores", "Marca", "Tipo de Producto", "Cantidad",  "Fecha de Vencimiento", "Estado"];

            $comienzo = 3;

            for ($i=0; $i < count($arrTitulos) ; $i++) {
               $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use($arrTitulos, $i) {
                       // manipulate the cel
                       $cell->setValue($arrTitulos[$i]);
                       $cell->setBackground('#D1E9F2');
                       $cell->setFontWeight('bold');
                       $cell->setBorder('thin', 'thin', 'thin', 'thin');
               });
            }
            $comienzo++;
            //dd($biopsias->get());
            $fecha_hoy = date('Y/m/d');
            foreach($fecha_cadu as $value){
               $arrValue = [];
               $estado = [];
                if ($value->fecha_vence < date('Y-m-d')) {
                    $estado ='Producto caducado';
                } else {
                    $estado ='Producto Proximo a Caducado';
                }

               $arrValue = [$value->codigo, $value->nombre, $value->descripcion, $value->serie."-", $value->pedido, $value->nombre_bodega, $value->nombrecomercial , $value->nombre_marca, $value->nombre_tipoproducto, $value->existencia, $value->fecha_vence, $estado];

               for ($i=0; $i < count($arrValue) ; $i++) {
                   $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use($arrValue, $i) {
                           // manipulate the cel
                           $cell->setValue($arrValue[$i]);
                           $cell->setBorder('thin', 'thin', 'thin', 'thin');
                   });
                }
                $comienzo++;
            }

          });


         })->export('xlsx');

    }

    public function buscador_usos(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $productos = [];
        //dd($request->all());
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $eq= $request['pciente'];
        $marca       = $request['marca'];
        $codigo      = $request['codigo'];
        $nombres    = $request['nombres'];
        $proveedores = $request['proveedor'];
        $serie       = $request['serie'];

        //dd($request->all());

        $productos = $this->busqueda($request)->paginate(20);

        $doc_bodega =InvDocumentosBodegas::all();
        $marca_nombre  = marca::where('estado', '1')->get();
        $proveedor     = proveedor::where('estado', '1')->get();


        return view('insumos/reporte/buscador_usos/index', ['productos' => $productos, 'nombres' => $nombres, "serie"=>$serie, 'proveedores' => $proveedores, 'proveedor' => $proveedor, 'codigo' => $codigo, 'marca' => $marca, 'marca' => $marca, 'marca_nombre' => $marca_nombre, 'fecha' => $fecha, 'paciente' => $eq, 'fecha_hasta' => $fecha_hasta, 'doc_bodega' => $doc_bodega, "data" => $request->all()]);
    }

    public function buscador_usos_farmacia(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $productos = [];
        //dd($request->all());
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $eq = $request['pciente'];
        $marca       = $request['marca'];
        $codigo      = $request['codigo'];
        $serie       = $request['serie'];
        $nombres    = $request['nombres'];
        $proveedores = $request['proveedor'];

        $productos = $this->busqueda_farmacia($request)->paginate(20);

        $doc_bodega = InvDocumentosBodegas::all();
        $marca_nombre  = marca::where('estado', '1')->get();
        $proveedor     = proveedor::where('estado', '1')->get();

        return view('insumos/reporte/buscador_usos/index', ['productos' => $productos, 'nombres' => $nombres, 'proveedores' => $proveedores, 'proveedor' => $proveedor, 'codigo' => $codigo, 'marca' => $marca, 'marca' => $marca, 'serie' => $serie, 'marca_nombre' => $marca_nombre, 'fecha' => $fecha, 'paciente' => $eq, 'fecha_hasta' => $fecha_hasta, 'doc_bodega' => $doc_bodega, "data" => $request->all()]);
    }

    public function descargar_dar_baja(Request $request)
    {
    $dar_baja=[];
    $fecha = ['fecha'];
    $fecha_hasta = ['fecha_hasta'];

    $dar_baja = ReportesController::busqueda_dar_baja($request)->paginate(20);
    //dd($dar_baja);

    return view('insumos/reporte/dar_baja/index', ['fecha'=>$fecha, 'fecha_hasta'=>$fecha_hasta, 'dar_baja'=>$dar_baja]);
    }

    public static function busqueda_dar_baja(Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $dar_baja = DB::table('inv_kardex as ik')
            ->join('producto as p', 'p.id', 'ik.id_producto')
            ->join('inv_det_movimientos as idm', 'idm.id', 'ik.id_inv_det_movimientos')
            ->join('marca as m', 'm.id', 'p.id_marca')
            ->join('pedido as ped', 'ped.id', 'idm.id_pedido')
            ->join('proveedor as prov', 'prov.id', 'ped.id_proveedor')
            ->where('ik.dar_baja', '1')
            ->where('ik.id_empresa', $id_empresa)
            ->select('ik.updated_at', 'p.nombre', 'idm.serie', 'ik.cantidad', 'ik.referencia', 'm.nombre as nombre_marca', 'ped.pedido as n_pedido','prov.razonsocial');

        if(count($request->all()) > 0){
            if(!is_null($request->fecha)){
                  $dar_baja = $dar_baja = $dar_baja->where('ik.updated_at', ">=" ,"{$request->fecha} 00:00:00");
                }

                if(!is_null($request->fecha_hasta)){
                   $dar_baja = $dar_baja = $dar_baja->where('ik.updated_at', "<=" ,"{$request->fecha_hasta} 23:59:59");
                 }
        }
        return $dar_baja;

    }

    public function reporte_dar_baja(Request $request)
    {
        
        $id_empresa  = $request->session()->get('id_empresa');

        $dar_baja = DB::table('inv_kardex as ik')
            ->join('producto as p', 'p.id', 'ik.id_producto')
            ->join('inv_det_movimientos as idm', 'idm.id', 'ik.id_inv_det_movimientos')
            ->join('marca as m', 'm.id', 'p.id_marca')            
            ->join('pedido as ped', 'ped.id', 'idm.id_pedido')
            ->join('proveedor as prov', 'prov.id', 'ped.id_proveedor')
            ->where('ik.dar_baja', '1')
            ->where('ik.id_empresa', $id_empresa)
            ->select('ik.updated_at', 'p.nombre', 'idm.serie', 'ik.cantidad', 'ik.referencia', 'm.nombre as nombre_marca', 'ped.pedido as n_pedido','prov.razonsocial');

        if(count($request->all()) > 0){
            if(!is_null($request->fecha)){
                  $dar_baja = $dar_baja = $dar_baja->where('ik.updated_at', ">=" ,"{$request->fecha} 00:00:00");
                }

                if(!is_null($request->fecha_hasta)){
                   $dar_baja = $dar_baja = $dar_baja->where('ik.updated_at', "<=" ,"{$request->fecha_hasta} 23:59:59");
                 }
           }


        $dar_baja=$dar_baja->get();
        //dd($dar_baja);

        $fecha_hoy = date('Y/m/d');

        Excel::create('Reporte_Dar_Baja'. $fecha_hoy , function ($excel) use ($dar_baja){

            $excel->sheet('Reporte Dar de Baja', function($sheet) use($dar_baja){
            $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");

                $fecha_hoy = date('Y/m/d');
                $sheet->mergeCells('A1:H2');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('Reporte Dar de Baja');
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('20');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


            $i = 3;

                $arrTitulos1 = ["Fecha", "Insumo", "Serie", "Cantidad", "Observación", "Marca", "Pedido", "Proveedor"];

                $comienzo = 3;

                for ($i = 0; $i < count($arrTitulos1); $i++) {
                    $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrTitulos1, $i) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos1[$i]);
                        $cell->setBackground('#D1E9F2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                //dd($biopsias->get());
                //dd($productos);
                foreach ($dar_baja as $value) {

                    $arrValue = [];


                    $arrValue = [date('Y-m-d', strtotime($value->updated_at)), $value->nombre, $value->serie, $value->cantidad, $value->referencia, $value->nombre_marca, $value->n_pedido, $value->razonsocial];

                    for ($i = 0; $i < count($arrValue); $i++) {
                        $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrValue, $i) {
                            // manipulate the cel
                            $cell->setValue($arrValue[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }
            });
        })->export('xlsx');
    }

    public static function busqueda($request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $productos = DB::table('inv_kardex as ik')
            ->join('inv_det_movimientos as idm', 'idm.id', 'ik.id_inv_det_movimientos')
            ->join('movimiento_paciente as mp', 'mp.id', 'idm.id_movimiento_paciente')
            ->join('pedido as p', 'p.id', 'idm.id_pedido')
            ->join('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->join('inv_documentos_bodegas as idb', 'idb.id', 'p.tipo')
            ->join('bodega as b', 'b.id', 'ik.id_bodega')
            ->join('producto as p1', 'p1.id', 'ik.id_producto')
            ->join('hc_procedimientos as hp', 'hp.id', 'idm.id_procedimiento')
            ->join('historiaclinica as h', 'h.hcid', 'hp.id_hc')
            ->join('seguros as s', 's.id','h.id_seguro')
            ->join('marca as m', 'm.id', 'p1.id_marca')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('planilla_detalle as pd', 'pd.id_movimiento_paciente','mp.id')
            ->join('planilla_cabecera as pc', 'pd.id_planilla_cabecera','pc.id')
            ->leftjoin('ct_asientos_cabecera as ac','pc.id_asiento_cabecera','ac.id')
            //->join('paciente as p2', 'p2.id','h.id_paciente')
            //->join('procedimiento as proc', 'proc.id', 'hp.id')
            ->leftjoin('paciente as u', 'u.id', 'h.id_paciente')
            ->whereNull('p1.cantidad_usos_kardex')
            
            //->where('pd.id_movimiento_paciente','=','mp.id')
            ->where('p.id_empresa', $id_empresa)
            ->where('ik.id_empresa', $id_empresa)
            ->where('pd.check','1')
            ->where('pd.estado','1')
            //->leftjoin('tipo as t', 't.id', 'p.tipo_producto')
            //->where('m.tipo', 0)
            //->select('p.codigo', 'p.id as producto_id', 'p.nombre as nombre_producto', 'm.serie', 'ped.pedido', 'mar.nombre as nombre_marca', 'b.nombre as nombre_bodega', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'hc.fecha_atencion', 'hc_prod.id','t.nombre as nombre_tipo','m.fecha_vencimiento');
            ->groupby('pd.id_movimiento_paciente')
            ->select('pd.id_movimiento_paciente as id_movimiento_paciente','p1.cantidad_usos_kardex', 'ik.valor_unitario as precio_k', 'p1.codigo', 'p.id as id_pedido', 'b.nombre as nombre_bodega', 'p1.nombre as nombre_producto', 'prov.nombrecomercial', 'a.fechaini', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'm.nombre as nombre_m', 'ik.descripcion', 'ik.referencia', 'idm.cantidad', 'idm.lote', 'p.vencimiento', 'p.pedido', 'idm.serie', 'idm.total', 'idb.documento', 'hp.id as hc_id_procedimiento', 'hp.id_doctor_examinador', 'idm.valor_unitario', 'idm.descuento','ac.fecha_asiento','ac.id as id_asiento','s.nombre as nombre_seguro','idm.fecha_vence');

        //dd($productos->get());
        if (count($request->all()) > 0) {
            if (!is_null($request->codigo)) {
                $productos = $productos->where('p1.codigo', $request->codigo);
            }
            if (!is_null($request->nombres)) {
                $productos = $productos->where('p1.nombre', 'like', '%' . $request->nombres . '%');
            }
            if (!is_null($request->tipo_)) {
                $productos = $productos->where("p.tipo", $request->tipo_);
            }
            if (!is_null($request->proveedor)) {
                $productos = $productos->where('prov.id', $request->proveedor);
            }

            if (!is_null($request->pciente)) {
                $eq = $request->pciente;
                $productos = $productos->where(function ($jq1) use ($eq) {
                    $jq1->orwhereraw('CONCAT(u.nombre1," ",u.nombre2," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $eq . '%'])
                        ->orwhereraw('CONCAT(u.nombre1," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $eq . '%']);
                });
            }

            if (!is_null($request->fecha)) {
                $productos = $productos = $productos->where('a.fechaini', ">=", "{$request->fecha} 00:00:00");
            }

            if (!is_null($request->fecha_hasta)) {
                $productos = $productos = $productos->where('a.fechaini', "<=", "{$request->fecha_hasta} 23:59:59");
            }
            if (!is_null($request->marca)) {
                $productos = $productos = $productos->where('m.id', $request->marca);
            }
            if (!is_null($request->serie)) {
                $productos  = $productos->where('idm.serie', "like", "%{$request->serie}");
            }
        }

        return $productos;
    }

    public static function busqueda_farmacia($request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $productos = DB::table('inv_kardex as ik')
            ->join('inv_det_movimientos as idm', 'idm.id', 'ik.id_inv_det_movimientos')
            //->join('movimiento_paciente as mp', 'mp.id', 'idm.id_movimiento_paciente')
            ->join('pedido as p', 'p.id', 'idm.id_pedido')
            ->join('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->join('inv_documentos_bodegas as idb', 'idb.id', 'p.tipo')
            ->join('bodega as b', 'b.id', 'ik.id_bodega')
            ->join('producto as p1', 'p1.id', 'ik.id_producto')
            ->join('hc_procedimientos as hp', 'hp.id', 'idm.id_procedimiento')
            ->join('historiaclinica as h', 'h.hcid', 'hp.id_hc')
            ->join('marca as m', 'm.id', 'p1.id_marca')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            //->join('planilla_detalle as pd', 'pd.id_movimiento_paciente','mp.id')
            ->leftjoin('paciente as u', 'u.id', 'h.id_paciente')
            ->whereNull('p1.cantidad_usos_kardex')
            ->where('p.id_empresa', $id_empresa)
            ->where('ik.id_empresa', $id_empresa)
            ->where('b.prioridad_farmacia', 1)
            ->where('p1.farmacia', 1)
            //->where('pd.check','1')
            //->where('pd.estado','1')
            //->groupby('pd.id_movimiento_paciente')
            ->select('p1.cantidad_usos_kardex', 'ik.valor_unitario as precio_k', 'p1.codigo', 'p.id as id_pedido', 'b.nombre as nombre_bodega', 'p1.nombre as nombre_producto', 'prov.nombrecomercial', 'a.fechaini', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'm.nombre as nombre_m', 'ik.descripcion', 'ik.referencia', 'idm.cantidad', 'idm.lote', 'p.vencimiento', 'p.pedido', 'idm.serie', 'idm.total', 'idb.documento', 'hp.id as hc_id_procedimiento', 'hp.id_doctor_examinador', 'idm.valor_unitario', 'idm.descuento');

        if (count($request->all()) > 0) {
            if (!is_null($request->codigo)) {
                $productos = $productos->where('p1.codigo', $request->codigo);
            }
            if (!is_null($request->nombres)) {
                $productos = $productos->where('p1.nombre', 'like', '%' . $request->nombres . '%');
            }
            if (!is_null($request->tipo_)) {
                $productos = $productos->where("p.tipo", $request->tipo_);
            }
            if (!is_null($request->proveedor)) {
                $productos = $productos->where('prov.id', $request->proveedor);
            }

            if (!is_null($request->pciente)) {
                $eq = $request->pciente;
                $productos = $productos->where(function ($jq1) use ($eq) {
                    $jq1->orwhereraw('CONCAT(u.nombre1," ",u.nombre2," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $eq . '%'])
                        ->orwhereraw('CONCAT(u.nombre1," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $eq . '%']);
                });
            }

            if (!is_null($request->fecha)) {
                $productos = $productos = $productos->where('a.fechaini', ">=", "{$request->fecha} 00:00:00");
            }

            if (!is_null($request->fecha_hasta)) {
                $productos = $productos = $productos->where('a.fechaini', "<=", "{$request->fecha_hasta} 23:59:59");
            }
            if (!is_null($request->marca)) {
                $productos = $productos = $productos->where('m.id', $request->marca);
            }
            if (!is_null($request->serie)) {
                $productos  = $productos->where('idm.serie', "like", "%{$request->serie}");
            }
        }

        return $productos;
    }

    public function busqueda_consumibles(Request $request)
    {
        $fecha_desde = $request['fecha'];
        if (is_null($fecha_desde)) {
            $fecha_desde = date("Y-m-d");
        }
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $consumibles = DB::table('inv_kardex as ik')
            ->join('inv_documentos_bodegas as idb', 'idb.id', 'ik.id_documento_bodega')
            ->join('inv_det_movimientos as idm', 'idm.id', 'ik.id_inv_det_movimientos')
            ->join('pedido as p', 'p.id', 'idm.id_pedido')
            ->join('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->join('bodega as b', 'b.id', 'ik.id_bodega')
            ->join('producto as p1', 'p1.id', 'ik.id_producto')
            ->join('marca as m', 'm.id', 'p1.id_marca')
            ->join('inv_cab_movimientos as icm', 'icm.id_pedido', 'p.id')
            ->join('ct_asientos_cabecera as ac','icm.id_asiento','ac.id')
            ->where('idb.abreviatura_documento', 'EVC');


        if (!is_null($fecha_desde)) {
            $consumibles = $consumibles->whereBetween('ik.fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        } else {
            $consumibles = $consumibles->where('ik.fecha', '<=', $fecha_hasta);
        }

        $consumibles = $consumibles->select('prov.nombrecomercial as nombreprov', 'ik.valor_unitario as precio_k', 'p1.codigo', 'b.nombre as nombre_bodega', 'p1.nombre as nombre_producto', 'prov.nombrecomercial', 'm.nombre as nombre_m', 'ik.descripcion', 'ik.referencia', 'idm.cantidad', 'idm.lote', 'p.vencimiento', 'p.pedido', 'idm.serie', 'idm.total', 'idb.documento', 'idm.valor_unitario', 'ik.fecha as fecha_kardex', 'idm.descuento','ac.fecha_asiento','ac.id as id_asiento');

        return $consumibles;
    }
    public function busqueda_consumibles_consigna(Request $request)
    {
        $fecha_desde = $request['fecha'];
        if (is_null($fecha_desde)) {
            $fecha_desde = date("Y-m-d");
        }
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }
        $consumibles_consig = DB::table('inv_kardex as ik')
            ->join('inv_documentos_bodegas as idb', 'idb.id', 'ik.id_documento_bodega')
            ->join('inv_det_movimientos as idm', 'idm.id', 'ik.id_inv_det_movimientos')
            ->join('pedido as p', 'p.id', 'idm.id_pedido')
            ->join('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->join('bodega as b', 'b.id', 'ik.id_bodega')
            ->join('producto as p1', 'p1.id', 'ik.id_producto')
            ->join('marca as m', 'm.id', 'p1.id_marca')
            ->join('inv_cab_movimientos as icm', 'icm.id_pedido', 'p.id')
            ->join('ct_asientos_cabecera as ac','icm.id_asiento','ac.id')
            ->where('idb.abreviatura_documento', 'ECC');


        if (!is_null($fecha_desde)) {
            $consumibles_consig = $consumibles_consig->whereBetween('ik.fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        } else {
            $consumibles_consig = $consumibles_consig->where('ik.fecha', '<=', $fecha_hasta);
        }

        $consumibles_consig = $consumibles_consig->select('prov.nombrecomercial as nombreprov', 'ik.valor_unitario as precio_k', 'p1.codigo', 'b.nombre as nombre_bodega', 'p1.nombre as nombre_producto', 'prov.nombrecomercial', 'm.nombre as nombre_m', 'ik.descripcion', 'ik.referencia', 'idm.cantidad', 'idm.lote', 'p.vencimiento', 'p.pedido', 'idm.serie', 'idm.total', 'idb.documento', 'idm.valor_unitario', 'ik.fecha as fecha_kardex', 'idm.descuento','ac.fecha_asiento','ac.id as id_asiento');

        return $consumibles_consig;
    }

    public static function busqueda_kardex_usos($request)
    {
        // if (!is_null($doc_bodega)){
        // if(!is_null($eq)){

        $id_empresa  = $request->session()->get('id_empresa');
        $productos_usos = DB::table('inv_kardex_nuevo as ikn')
            ->join('inv_det_movimientos as idm', 'idm.id', 'ikn.id_inv_det_movimientos')
            ->join('movimiento_paciente as mp', 'mp.id', 'idm.id_movimiento_paciente')
            ->join('pedido as p', 'p.id', 'idm.id_pedido')
            ->join('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->join('inv_documentos_bodegas as idb', 'idb.id', 'p.tipo')
            ->join('bodega as b', 'b.id', 'ikn.id_bodega')
            ->join('producto as p1', 'p1.id', 'ikn.id_producto')
            ->join('hc_procedimientos as hp', 'hp.id', 'idm.id_procedimiento')
            ->join('historiaclinica as h', 'h.hcid', 'hp.id_hc')
            ->join('marca as m', 'm.id', 'p1.id_marca')
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->leftjoin('paciente as u', 'u.id', 'h.id_paciente')
            ->join('planilla_detalle as pd', 'pd.id_movimiento_paciente','mp.id')
            ->join('planilla_cabecera as pc', 'pd.id_planilla_cabecera','pc.id')
            ->leftjoin('ct_asientos_cabecera as ac','pc.id_asiento_cabecera','ac.id')
            ->where('p.id_empresa', $id_empresa)
            ->where('ikn.id_empresa', $id_empresa)
            ->select('ikn.fecha as fecha_kardex', 'ikn.valor_unitario as precio_k', 'p1.codigo', 'p.id as id_pedido', 'b.nombre as nombre_bodega', 'p1.nombre as nombre_producto', 'prov.nombrecomercial', 'a.fechaini', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'm.nombre as nombre_m', 'ikn.descripcion', 'ikn.referencia', 'idm.cantidad', 'idm.lote', 'p.vencimiento', 'p.pedido', 'idm.serie', 'idm.total', 'idb.documento', 'hp.id as hc_id_procedimiento','ac.fecha_asiento','ac.id as id_asiento', 'hp.id_doctor_examinador', 'idm.valor_unitario');

        //dd($productos->get());
        if (count($request->all()) > 0) {
            if (!is_null($request->codigo)) {
                $productos_usos = $productos_usos->where('p1.codigo', $request->codigo);
            }
            if (!is_null($request->nombres)) {
                $productos_usos = $productos_usos->where('p1.nombre', 'like', '%' . $request->nombres . '%');
            }
            if (!is_null($request->tipo_)) {
                $productos_usos = $productos_usos->where("p.tipo", $request->tipo_);
            }
            if (!is_null($request->proveedor)) {
                $productos_usos = $productos_usos->where('prov.id', $request->proveedor);
            }

            if (!is_null($request->pciente)) {
                $eq = $request->pciente;
                $productos_usos = $productos_usos->where(function ($jq1) use ($eq) {
                    $jq1->orwhereraw('CONCAT(u.nombre1," ",u.nombre2," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $eq . '%'])
                        ->orwhereraw('CONCAT(u.nombre1," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $eq . '%']);
                });
            }

            if (!is_null($request->fecha)) {
                $productos_usos = $productos_usos->where('a.fechaini', ">=", "{$request->fecha} 00:00:00");
            }

            if (!is_null($request->fecha_hasta)) {
                $productos_usos  = $productos_usos->where('a.fechaini', "<=", "{$request->fecha_hasta} 23:59:59");
            }
            if (!is_null($request->marca)) {
                $productos_usos  = $productos_usos->where('m.id', $request->marca);
            }
        }
        return $productos_usos;
    }
    public static function busqueda_productos_proforma($request)
    {
        $fecha_desde = $request['fecha'];
        if (is_null($fecha_desde)) {
            $fecha_desde = date("Y-m-d");
        }
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y-m-d');
        }

        $serie       = $request['serie'];
        $marca       = $request['marca'];
        $codigo      = $request['codigo'];
        $nombres    = $request['nombres'];
        $proveedores = $request['proveedor'];
        
        $id_empresa  = $request->session()->get('id_empresa');
        $prod_proforma = DB::table('venta_kardex as vk')
            ->join('inv_cab_movimientos as icm', 'icm.id','vk.id_cab_moviento')
            ->join('inv_det_movimientos as idm', 'idm.id_inv_cab_movimientos', 'icm.id')
            ->join('inv_kardex as ik','ik.id_inv_det_movimientos','idm.id')
            ->join('pedido as p', 'p.id', 'idm.id_pedido')
            ->join('proveedor as prov', 'prov.id', 'p.id_proveedor')
            ->join('inv_documentos_bodegas as idb', 'idb.id', 'p.tipo')
            ->join('bodega as b', 'b.id', 'ik.id_bodega')
            ->join('producto as p1', 'p1.id', 'ik.id_producto')
            ->join('marca as m', 'm.id', 'p1.id_marca')
            ->join('ct_clientes as cli','cli.identificacion','b.id_cliente')
            ->where('p.id_empresa', $id_empresa)
            ->where('ik.id_empresa', $id_empresa)
            ->groupby('vk.id_cab_moviento','idm.serie','idm.cantidad');
            if (!is_null($fecha_desde)) {
                $prod_proforma = $prod_proforma->whereBetween('ik.fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
            } else {
                $prod_proforma = $prod_proforma->where('ik.fecha', '<=', $fecha_hasta);
            }
            if (!is_null($request->marca)) {
                $prod_proforma = $prod_proforma = $prod_proforma->where('m.id', $request->marca);
            }
            if (!is_null($request->marca)) {
                $prod_proforma = $prod_proforma = $prod_proforma->where('m.id', $request->marca);
            }
            if (!is_null($request->serie)) {
                $prod_proforma  = $prod_proforma->where('idm.serie', "like", "%{$serie}");
            }
            if (!is_null($request->codigo)) {
                $prod_proforma = $prod_proforma->where('p1.codigo', $request->codigo);
            }
            if (!is_null($request->nombres)) {
                $prod_proforma = $prod_proforma->where('p1.nombre', 'like', '%' . $request->nombres . '%');
            }
            if (!is_null($request->tipo_)) {
                $prod_proforma = $prod_proforma->where("p.tipo", $request->tipo_);
            }
            if (!is_null($request->proveedor)) {
                $prod_proforma = $prod_proforma->where('prov.id', $request->proveedor);
            }
            $prod_proforma = $prod_proforma->select('vk.id_venta','prov.nombrecomercial as nombreprov','ik.valor_unitario as precio_k', 'p1.codigo', 'p.id as id_pedido', 'b.nombre as nombre_bodega', 'p1.nombre as nombre_producto', 'prov.nombrecomercial', 'm.nombre as nombre_m', 'ik.descripcion', 'ik.referencia', 'idm.cantidad', 'idm.lote', 'p.vencimiento', 'p.pedido', 'idm.serie', 'idm.total', 'idb.documento',  'idm.valor_unitario', 'idm.descuento','cli.identificacion','ik.fecha as fecha_kardex','cli.nombre as nombre_cliente');
            //dd($prod_proforma->get());

        return $prod_proforma;
    }


    public function reporte_usos_productos(Request $request)
    {
        //    dd($request->all());
        $productos = ReportesController::busqueda($request)->get();
        $consumibles = ReportesController::busqueda_consumibles($request)->get();
        $consumibles_consig = ReportesController::busqueda_consumibles_consigna($request)->get();
        $productos_usos = ReportesController::busqueda_kardex_usos($request)->get();
        $prod_proforma = ReportesController::busqueda_productos_proforma($request)->get();
         /*$idusuario  = Auth::user()->id;
         if($idusuario == '0941096372')
         {
             dd($consumibles);
         }*/

        $fecha_d = date('Y/m/d');

        Excel::create('Reporte_Uso_Productos' . $fecha_d, function ($excel) use ($productos, $consumibles, $productos_usos,$prod_proforma,$consumibles_consig) {

            $excel->sheet('Reporte Uso de Productos', function ($sheet) use ($productos, $consumibles, $productos_usos,$prod_proforma,$consumibles_consig) {
                $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");

                $fecha_d = date('Y/m/d');
                $sheet->mergeCells('A1:T2');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('Reporte Uso de Productos');
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('20');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $i = 3;

                $arrTitulos = ["Fecha de Utilización", "Paciente", "Marca", "Detalle", "Cantidad", "Lote", "Fecha de Vencimiento", "Pedido", "Serie", "Precio Kardex", "Precio Pedido", "Precio Kardex",  "Total Kardex", "Factura/Consigna",  "Procedimiento", "Médico que realizo el procedimiento", "Bodega","Fecha Asiento","# Asiento", "Tipo Seguro"];

                $comienzo = 3;

                for ($i = 0; $i < count($arrTitulos); $i++) {
                    $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrTitulos, $i) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos[$i]);
                        $cell->setBackground('#D1E9F2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                //dd($biopsias->get());
                //dd($productos);
                $fecha_asiento = "";
                $id_asiento = "";
                foreach ($productos as $value) {

                    $arrValue = [];
                    $nombre_proc = "";
                    $nombre_doc = "";
                    $precio_pedido = [];
                    $precio_total = [];
                    $procedimiento_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->hc_id_procedimiento)->first();

                    //dd($value);
                    $id_pedido = $value->id_pedido;
                    $det_movimientos = InvDetMovimientos::where('id_pedido', $id_pedido)->where('serie', $value->serie)->first();
                    $precio_pedido = $det_movimientos->valor_unitario;
                    if (!is_null($procedimiento_final)) {
                        if (isset($procedimiento_final->procedimiento)) {
                            $nombre_proc = $procedimiento_final->procedimiento->nombre;
                        }
                    }

                    if (!is_null($value->id_doctor_examinador)) {
                        //$nombre_doc = User::where('id', $value->id_doctor_examinador)->first();
                        $nom_doc = User::where('id', $value->id_doctor_examinador)->first();
                        if (!is_null($nom_doc)) {
                            $nombre_doc = $nom_doc->nombre1 . ' ' . $nom_doc->apellido1;
                        }
                    }
                    if(!is_null($fecha_asiento)){
                        $fecha_asiento =  $value->fecha_asiento;
                    }
                    if(!is_null($id_asiento)){
                        $id_asiento =  $value->id_asiento;
                    }

                    $precio_total =  $value->cantidad *  $value->precio_k;

                    $arrValue = [$value->fechaini, " {$value->apellido1} {$value->apellido2} {$value->nombre1} {$value->nombre2}", $value->nombre_m, $value->nombre_producto, $value->cantidad, $value->lote, $value->vencimiento, $value->pedido, $value->serie . "-", $value->precio_k, $precio_pedido, $value->precio_k, $precio_total, $value->documento,  $nombre_proc, $nombre_doc, $value->nombre_bodega,$fecha_asiento,$id_asiento, $value->nombre_seguro];

                    for ($i = 0; $i < count($arrValue); $i++) {
                        $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrValue, $i) {
                            // manipulate the cel
                            $cell->setValue($arrValue[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }

                $comienzo++;


                $comienzo++;

                $arrTitulos4 = ["INSUMOS POR USOS"];
                for ($b = 0; $b < count($arrTitulos4); $b++) {
                    $sheet->cell("{$abec[$b]}{$comienzo}", function ($cell) use ($arrTitulos4, $b) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos4[$b]);
                        $cell->setBackground('#D1E9F2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;

                $arrTitulos3 = ["Fecha de Utilización", "Paciente", "Marca", "Detalle", "Cantidad", "Lote", "Fecha de Vencimiento", "Pedido", "Serie", "Precio Kardex", "Precio Pedido", "Precio Kardex",  "Total Kardex", "Factura/Consigna",  "Procedimiento", "Médico que realizo el procedimiento", "Bodega","Fecha Asiento","# Asiento"];
                for ($z = 0; $z < count($arrTitulos3); $z++) {
                    $sheet->cell("{$abec[$z]}{$comienzo}", function ($cell) use ($arrTitulos3, $z) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos3[$z]);
                        $cell->setBackground('#D1E9F2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;

                foreach ($productos_usos as $value3) {
                    $arrValue3 = [];
                    $nombre_proc = "";
                    $nombre_doc = "";
                    $precio_pedido = [];
                    $precio_total = [];
                    $procedimiento_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $value3->hc_id_procedimiento)->first();

                    $id_pedido = $value3->id_pedido;
                    $det_movimientos = InvDetMovimientos::where('id_pedido', $id_pedido)->where('serie', $value3->serie)->first();


                    $precio_pedido = $det_movimientos->valor_unitario;

                    $precio_total = $value3->cantidad *  $value3->precio_k;


                    if (!is_null($procedimiento_final)) {
                        if (isset($procedimiento_final->procedimiento)) {
                            $nombre_proc = $procedimiento_final->procedimiento->nombre;
                        }
                    }

                    if (!is_null($value3->id_doctor_examinador)) {
                        //$nombre_doc = User::where('id', $value3->id_doctor_examinador)->first();
                        $nom_doc = User::where('id', $value3->id_doctor_examinador)->first();
                        if (!is_null($nom_doc)) {
                            $nombre_doc = $nom_doc->nombre1 . ' ' . $nom_doc->apellido1;
                        }
                    }
                    if(!is_null($fecha_asiento)){
                        $fecha_asiento =  $value3->fecha_asiento;
                    }
                    if(!is_null($id_asiento)){
                        $id_asiento =  $value3->id_asiento;
                    }

                    $arrValue3 = [$value3->fechaini, " {$value3->apellido1} {$value3->apellido2} {$value3->nombre1} {$value3->nombre2}", $value3->nombre_m, $value3->nombre_producto, $value3->cantidad, $value3->lote, $value3->vencimiento, $value3->pedido, $value3->serie . "-", $value3->precio_k, $precio_pedido, $value3->precio_k, $precio_total, $value3->documento,  $nombre_proc, $nombre_doc, $value3->nombre_bodega,$fecha_asiento,$id_asiento];

                    for ($a = 0; $a < count($arrValue3); $a++) {
                        $sheet->cell("{$abec[$a]}{$comienzo}", function ($cell) use ($arrValue3, $a) {
                            // manipulate the cel
                            $cell->setValue($arrValue3[$a]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }
                $comienzo++;

                $arrTitulos6 = ["CONSUMIBLES"];
                for ($c = 0; $c < count($arrTitulos6); $c++) {
                    $sheet->cell("{$abec[$c]}{$comienzo}", function ($cell) use ($arrTitulos6, $c) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos6[$c]);
                        $cell->setBackground('#D1E9F2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;

                $arrTitulos2 = ["Fecha", "Marca", "Detalle", "Cantidad", "Lote", "Fecha de Vencimiento", "Pedido", "Proveedor", "Serie", "Precio Kardex", "Precio Pedido", "Precio Kardex",  "Total Kardex", "Documento", "Bodega","Fecha Asiento","# Asiento"];
                for ($x = 0; $x < count($arrTitulos2); $x++) {
                    $sheet->cell("{$abec[$x]}{$comienzo}", function ($cell) use ($arrTitulos2, $x) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos2[$x]);
                        $cell->setBackground('#D1E9F2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;

                foreach ($consumibles as $value2) {
                    $arrValue2 = [];
                    $precio_total = [];

                    if (!is_null($value2->nombreprov)) {
                        $nombre_proveedor = $value2->nombreprov;
                    } else {
                        $nombre_proveedor = "N/A";
                    }
                    $precio_total = $value2->cantidad *  $value2->precio_k;
                    if(!is_null($fecha_asiento)){
                        $fecha_asiento =  $value2->fecha_asiento;
                    }
                    if(!is_null($id_asiento)){
                        $id_asiento =  $value2->id_asiento;
                    }

                    $arrValue2 = [$value2->fecha_kardex, $value2->nombre_m, $value2->nombre_producto, $value2->cantidad, $value2->lote, $value2->vencimiento, $value2->pedido, $nombre_proveedor, $value2->serie . "-", $value2->precio_k, $value2->valor_unitario, $value2->precio_k, $precio_total, $value2->documento, $value2->nombre_bodega,$fecha_asiento,$id_asiento];

                    for ($y = 0; $y < count($arrValue2); $y++) {
                        $sheet->cell("{$abec[$y]}{$comienzo}", function ($cell) use ($arrValue2, $y) {
                            // manipulate the cel
                            $cell->setValue($arrValue2[$y]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }
                $comienzo++;

                $arrTitulos9 = ["CONSUMIBLES EN CONSIGNA"];
                for ($c = 0; $c < count($arrTitulos9); $c++) {
                    $sheet->cell("{$abec[$c]}{$comienzo}", function ($cell) use ($arrTitulos9, $c) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos9[$c]);
                        $cell->setBackground('#D1E9F2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;

                $arrTitulos10 = ["Fecha", "Marca", "Detalle", "Cantidad", "Lote", "Fecha de Vencimiento", "Pedido", "Proveedor", "Serie", "Precio Kardex", "Precio Pedido", "Precio Kardex",  "Total Kardex", "Documento", "Bodega","Fecha Asiento","# Asiento"];
                for ($x = 0; $x < count($arrTitulos10); $x++) {
                    $sheet->cell("{$abec[$x]}{$comienzo}", function ($cell) use ($arrTitulos10, $x) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos10[$x]);
                        $cell->setBackground('#D1E9F2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;

                foreach ($consumibles_consig as $value5) {
                    $arrValue2 = [];
                    $precio_total = [];

                    if (!is_null($value5->nombreprov)) {
                        $nombre_proveedor = $value5->nombreprov;
                    } else {
                        $nombre_proveedor = "N/A";
                    }
                    $precio_total = $value5->cantidad *  $value5->precio_k;
                    if(!is_null($fecha_asiento)){
                        $fecha_asiento =  $value5->fecha_asiento;
                    }
                    if(!is_null($id_asiento)){
                        $id_asiento =  $value5->id_asiento;
                    }

                    $arrValue5 = [$value5->fecha_kardex, $value5->nombre_m, $value5->nombre_producto, $value5->cantidad, $value5->lote, $value5->vencimiento, $value5->pedido, $nombre_proveedor, $value5->serie . "-", $value5->precio_k, $value5->valor_unitario, $value5->precio_k, $precio_total, $value5->documento, $value5->nombre_bodega,$fecha_asiento,$id_asiento];

                    for ($y = 0; $y < count($arrValue5); $y++) {
                        $sheet->cell("{$abec[$y]}{$comienzo}", function ($cell) use ($arrValue5, $y) {
                            // manipulate the cel
                            $cell->setValue($arrValue5[$y]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }
                $comienzo++;

                $arrTitulos7 = ["CONSUMO CONSIGNA FILIALES"];
                for ($c = 0; $c < count($arrTitulos7); $c++) {
                    $sheet->cell("{$abec[$c]}{$comienzo}", function ($cell) use ($arrTitulos7, $c) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos7[$c]);
                        $cell->setBackground('#D7BDE2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;

                $arrTitulos8 = ["Fecha", "Marca", "Detalle", "Cantidad", "Lote", "Fecha de Vencimiento", "Pedido", "Proveedor", "Serie", "Precio Kardex", "Precio Pedido", "Precio Kardex",  "Total Kardex", "Documento", "Bodega", "Cliente","ID Fac. Venta"];
                for ($x = 0; $x < count($arrTitulos8); $x++) {
                    $sheet->cell("{$abec[$x]}{$comienzo}", function ($cell) use ($arrTitulos8, $x) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos8[$x]);
                        $cell->setBackground('#D7BDE2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;

                foreach ($prod_proforma as $value4) {
                    $arrValue4 = [];
                    $precio_total = [];

                    if (!is_null($value4->nombreprov)) {
                        $nombre_proveedor = $value4->nombreprov;
                    } else {
                        $nombre_proveedor = "N/A";
                    }
                    if(!is_null($value4->identificacion)){
                        $nombre_cliente = $value4->nombre_cliente;
                    }else{
                        $nombre_cliente = "N/A";
                    }
                    $precio_total = $value4->cantidad *  $value4->precio_k;

                    $arrValue4 = [$value4->fecha_kardex, $value4->nombre_m, $value4->nombre_producto, $value4->cantidad, $value4->lote, $value4->vencimiento, $value4->pedido, $nombre_proveedor, $value4->serie . "-", $value4->precio_k, $value4->valor_unitario, $value4->precio_k, $precio_total, $value4->documento, $value4->nombre_bodega,$nombre_cliente, $value4->id_venta];

                    for ($y = 0; $y < count($arrValue4); $y++) {
                        $sheet->cell("{$abec[$y]}{$comienzo}", function ($cell) use ($arrValue4, $y) {
                            // manipulate the cel
                            $cell->setValue($arrValue4[$y]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }
                $comienzo++;
            });
        })->export('xlsx');
    }


    public function reporte_usos_productos_farmacia(Request $request)
    {
        $productos = ReportesController::busqueda_farmacia($request)->get();

        $fecha_d = date('Y/m/d');

        Excel::create('Reporte_Uso_Productos' . $fecha_d, function ($excel) use ($productos) {

            $excel->sheet('Reporte Uso de Productos', function ($sheet) use ($productos) {
                $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");

                $fecha_d = date('Y/m/d');
                $sheet->mergeCells('A1:Q2');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('Reporte Uso de Productos de Farmacia');
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('20');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i = 3;

                $arrTitulos = ["Fecha de Utilización", "Paciente", "Marca", "Detalle", "Cantidad", "Lote", "Fecha de Vencimiento", "Pedido", "Serie", "Precio Kardex", "Precio Pedido", "Precio Kardex",  "Total Kardex", "Factura/Consigna",  "Procedimiento", "Médico que realizo el procedimiento/Consulta", "Bodega"];

                $comienzo = 3;

                for ($i = 0; $i < count($arrTitulos); $i++) {
                    $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrTitulos, $i) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos[$i]);
                        $cell->setBackground('#D1E9F2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                foreach ($productos as $value) {

                    $arrValue = [];
                    $nombre_proc = "";
                    $nombre_doc = "";
                    $precio_pedido = [];
                    $precio_total = [];
                    $procedimiento_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->hc_id_procedimiento)->first();
                    //dd($value);
                    $id_pedido = $value->id_pedido;
                    $det_movimientos = InvDetMovimientos::where('id_pedido', $id_pedido)->where('serie', $value->serie)->first();
                    $precio_pedido = $det_movimientos->valor_unitario;
                    if (!is_null($procedimiento_final)) {
                        if (isset($procedimiento_final->procedimiento)) {
                            $nombre_proc = $procedimiento_final->procedimiento->nombre;
                        }
                    }else{
                        $nombre_proc = "Consulta";
                    }

                    if (!is_null($value->id_doctor_examinador)) {
                        //$nombre_doc = User::where('id', $value->id_doctor_examinador)->first();
                        $nom_doc = User::where('id', $value->id_doctor_examinador)->first();
                        if (!is_null($nom_doc)) {
                            $nombre_doc = $nom_doc->nombre1 . ' ' . $nom_doc->apellido1;
                        }
                    }

                    $precio_total =  $value->cantidad *  $value->precio_k;

                    $arrValue = [$value->fechaini, " {$value->apellido1} {$value->apellido2} {$value->nombre1} {$value->nombre2}", $value->nombre_m, $value->nombre_producto, $value->cantidad, $value->lote, $value->vencimiento, $value->pedido, $value->serie . "-", $value->precio_k, $value->valor_unitario, $value->precio_k, $precio_total, $value->documento,  $nombre_proc, $nombre_doc, $value->nombre_bodega];

                    for ($i = 0; $i < count($arrValue); $i++) {
                        $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrValue, $i) {
                            // manipulate the cel
                            $cell->setValue($arrValue[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }
            });
        })->export('xlsx');
    }


    public function buscador_usos_equipo(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $fecha = $request['fecha'];
        if (is_null($fecha)) {
            $fecha = date('Y/m/d');
        }
        $fecha_hasta = $request['fecha_hasta'];
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y/m/d');
        }
        $productos = DB::table('equipo as e')
            ->join('equipo_historia as eh', 'eh.id_equipo', 'e.id')
            ->join('historiaclinica as hc', 'hc.hcid', 'eh.hcid')
            ->join('users as u', 'u.id', 'hc.id_paciente')
            ->where('e.nombre', 'like', '%' . $request['nombre'] . '%')
            ->where('e.tipo', 'like', '%' . $request['tipo'] . '%')
            ->where('e.modelo', 'like', '%' . $request['modelo'] . '%')
            ->where('e.marca', 'like', '%' . $request['marca'] . '%')
            ->where('e.serie', 'like', '%' . $request['serie'] . '%');
        //dd($productos->get());
        if ($fecha != null && $fecha_hasta != null) {
            $productos = $productos->whereBetween('hc.fecha_atencion', [$fecha, $fecha_hasta]);
        }

        $productos = $productos->paginate(20);

        return view('insumos/reporte/buscador_equipo/index', ['productos' => $productos, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'nombre' => $request['nombre'], 'tipo' => $request['tipo'], 'modelo' => $request['modelo'], 'marca' => $request['marca'], 'serie' => $request['serie']]);
    }

    public function buscador_equipo_excel(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];

        $productos = DB::table('equipo as e')
            ->join('equipo_historia as eh', 'eh.id_equipo', 'e.id')
            ->join('historiaclinica as hc', 'hc.hcid', 'eh.hcid')
            ->join('users as u', 'u.id', 'hc.id_paciente')
            ->where('e.nombre', 'like', '%' . $request['nombre'] . '%')
            ->where('e.tipo', 'like', '%' . $request['tipo'] . '%')
            ->where('e.modelo', 'like', '%' . $request['modelo'] . '%')
            ->where('e.marca', 'like', '%' . $request['marca'] . '%')
            ->where('e.serie', 'like', '%' . $request['serie'] . '%');
        //dd($productos->get());
        if ($fecha != null && $fecha_hasta != null) {
            $productos = $productos->whereBetween('hc.fecha_atencion', [$fecha, $fecha_hasta]);
        }

        $productos = $productos->get();

        Excel::create('Reporte de Equipos Usados ', function ($excel) use ($productos) {

            $excel->sheet(date('Y-m-d'), function ($sheet) use ($productos) {

                $sheet->mergeCells('A1:J1');

                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REPORTE DE EQUIPOS USADOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SERIE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');

                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MARCA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRESTAMO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA DE PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });

                $i = 3;
                foreach ($productos as $value) {
                    //dd($value);

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre);
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue("  " . $value->serie);
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->tipo);
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        //$cell->setDataType(PHPExcel_Cell_DataType::TYPE_STRING);
                        //$cell->getNumberFormat();
                        $cell->setValue($value->marca);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->prestamo == 1) {
                            $cell->setValue('Si');
                        } else {
                            $cell->setValue('No');
                        }
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->fecha_atencion);
                    });

                    $i++;
                }
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(100)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(40)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(40)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(40)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(50)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(20)->setAutosize(false);
            ob_end_clean();
        })->export('xlsx');
    }

    public function buscador_usos_excel(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $eq = $request['pciente'];
        if (!is_null($eq)) {
            $id_empresa  = $request->session()->get('id_empresa');

            $productos = DB::table('movimiento as m')
                ->leftjoin('producto as p', 'p.id', 'm.id_producto')
                ->join('pedido as ped', 'ped.id', 'm.id_pedido')
                ->join('marca as mar', 'mar.id', 'p.id_marca')
                ->join('bodega as b', 'b.id', 'm.id_bodega')
                ->leftjoin('movimiento_paciente as mp', 'mp.id_movimiento', 'm.id')
                ->leftjoin('hc_procedimientos as hc_prod', 'hc_prod.id', 'mp.id_hc_procedimientos')
                ->leftjoin('historiaclinica as hc', 'hc.hcid', 'hc_prod.id_hc')
                ->leftjoin('users as u', 'u.id', 'hc.id_paciente')
                ->leftjoin('tipo as t', 't.id', 'p.tipo_producto')
                ->where('m.tipo', 0)
                ->where('p.id_empresa', $id_empresa)
                ->select('p.codigo', 'p.id as producto_id', 'p.nombre as nombre_producto', 'm.serie', 'ped.pedido', 'mar.nombre as nombre_marca', 'b.nombre as nombre_bodega', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'hc.fecha_atencion', 'hc_prod.id', 't.nombre as nombre_tipo', 'm.fecha_vencimiento', 'm.lote');
            $nombres2 = explode(" ", $eq);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $productos = $productos->where(function ($jq1) use ($eq) {
                    $jq1->orwhereraw('CONCAT(u.nombre1," ",u.nombre2," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $eq . '%'])
                        ->orwhereraw('CONCAT(u.nombre1," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $eq . '%']);
                });
            } else {

                $productos = $productos->whereraw('CONCAT(u.nombre1," ",u.nombre2," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $eq . '%']);
            }
        } else {
            $id_empresa  = $request->session()->get('id_empresa');

            $productos = DB::table('movimiento as m')
                ->leftjoin('producto as p', 'p.id', 'm.id_producto')
                ->join('pedido as ped', 'ped.id', 'm.id_pedido')
                ->join('marca as mar', 'mar.id', 'p.id_marca')
                ->join('bodega as b', 'b.id', 'm.id_bodega')
                ->leftjoin('movimiento_paciente as mp', 'mp.id_movimiento', 'm.id')
                ->leftjoin('hc_procedimientos as hc_prod', 'hc_prod.id', 'mp.id_hc_procedimientos')
                ->leftjoin('historiaclinica as hc', 'hc.hcid', 'hc_prod.id_hc')
                ->leftjoin('users as u', 'u.id', 'hc.id_paciente')
                ->leftjoin('tipo as t', 't.id', 'p.tipo_producto')
                ->where('m.tipo', 0)
                ->where('p.id_empresa', $id_empresa)
                ->select('p.codigo', 'p.id as producto_id', 'p.nombre as nombre_producto', 'm.serie', 'ped.pedido', 'mar.nombre as nombre_marca', 'b.nombre as nombre_bodega', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'hc.fecha_atencion', 'hc_prod.id', 't.nombre as nombre_tipo', 'm.fecha_vencimiento', 'm.lote');
        }
        if ($fecha != null && $fecha_hasta != null) {
            $productos = $productos->whereBetween('hc.fecha_atencion', [$fecha, $fecha_hasta]);
        } else {
            $fecha       = date('Y-m-d');
            $fecha_hasta = date('Y-m-d');
            $productos   = $productos->whereBetween('hc.fecha_atencion', [$fecha, $fecha_hasta]);
        }

        $productos = $productos->get();

        $texto = "";
        foreach ($productos as $value) {
            $adicionales = Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id)->get();
            $mas         = true;
        }

        foreach ($adicionales as $value2) {
            if ($mas == true) {
                $texto = $texto . $value2->procedimiento->nombre;
                $mas   = false;
            } else {
                $texto = $texto . ' + ' . $value2->procedimiento->nombre;
            }
        }

        //dd($productos);

        Excel::create('Reporte de Productos Usados ', function ($excel) use ($productos, $texto) {

            $excel->sheet(date('Y-m-d'), function ($sheet) use ($productos, $texto) {

                $sheet->mergeCells('A1:L1');

                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REPORTE DE PRODUCTOS USADOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CODIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO DE PRODUCTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');

                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SERIE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');

                });
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PEDIDO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BODEGA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MARCA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });
                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA DE CADUCIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });

                $sheet->cell('L2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LOTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#000000');
                    $cell->setBackground('#DDF1FF');
                    $cell->setAlignment('center');
                });

                $i = 3;
                foreach ($productos as $value) {
                    //dd($value);

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->codigo);
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre_producto);
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre_tipo);
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue("  ".$value->serie);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        //$cell->setDataType(PHPExcel_Cell_DataType::TYPE_STRING);
                        //$cell->getNumberFormat();
                        $cell->setValue($value->pedido);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre_bodega);
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre_marca);
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->apellido1 . ' ' . $value->apellido2 . ' ' . $value->nombre1 . ' ' . $value->nombre2);
                    });
                    $sheet->cell('I' . $i, function ($cell) use ($value, $texto) {
                        // manipulate the cel
                        $cell->setValue($texto);
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->fecha_atencion);
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->fecha_vencimiento);
                    });
                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if(isset($value->lote)){
                            $cell->setValue($value->lote);
                        }

                    });

                    $i++;
                }

            });
            $cantidad_i=count($productos);
            $excel->getActiveSheet()->getStyle('D3:D'.$cantidad_i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $excel->getActiveSheet()->getStyle('A3:A'.$cantidad_i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(40)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(40)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(40)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(20)->setAutosize(false);
            ob_end_clean();
        })->export('xlsx');
    }

    public function modal_detalle($id)
    {
        $producto    = producto::find($id);
        $movimientos = movimiento::where('movimiento.id_producto', $id)
            ->leftjoin('movimiento_paciente as mp', 'mp.id_movimiento', 'movimiento.id')
            ->leftjoin('hc_procedimientos as hc_prod', 'hc_prod.id', 'mp.id_hc_procedimientos')
            ->leftjoin('historiaclinica as hc', 'hc.hcid', 'hc_prod.id_hc')
            ->leftjoin('users as u', 'u.id', 'hc.id_paciente')
            ->where('movimiento.tipo', 0)
            ->select('movimiento.serie', 'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'hc.fecha_atencion', 'hc_prod.id')
            ->get();
        return view('insumos/reporte/buscador_usos/modal', ['movimientos' => $movimientos, 'producto' => $producto]);
    }
    public function index_costos_planillas(Request $request){
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        if (!isset($request['fecha_desde']) or is_null($request['fecha_desde'])) {
            $request['fecha_desde'] = "01-01-" . date('Y');
        }
        if (!isset($request['fecha_hasta']) or is_null($request['fecha_hasta'])) {
            $request['fecha_hasta'] = "31-12-" . date('Y');
        }
        // dd($request->all());
        $fecha_desde = date("Y-m-d", strtotime($request['fecha_desde']));
        $fecha_hasta = date("Y-m-d", strtotime($request['fecha_hasta']));



        $planillas = DB::table('planilla_detalle as pd')
            ->join('planilla_cabecera as pc', 'pd.id_planilla_cabecera', 'pc.id')
            ->where('pc.aprobado', 1)
            ->where('pd.check', 1)
            ->where('pd.estado', 1)
            ->whereNotNull('pc.id_asiento_cabecera')
            ->groupby('pd.id_planilla_cabecera')
            //->sum('pd.precio')
            ->select('pc.id', 'pc.fecha', 'pc.observacion', 'pc.id_hc_procedimiento', 'pc.id_agenda', 'pd.precio', 'pc.aprobado', 'pd.estado', 'pd.check', 'pc.id_asiento_cabecera');


        if ($fecha_desde != null && $fecha_hasta != null) {
            $planillas = $planillas->whereBetween('pc.fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        }
        if ($fecha_desde != null) {
            $planillas = $planillas->where('pc.fecha', '>=', $fecha_desde);
        }
        if ($fecha_hasta != null) {
            $planillas = $planillas->where('pc.fecha', '<=', $fecha_hasta);
        }
        $planillas = $planillas->get();
        // dd($fecha_desde, $fecha_hasta);
        // return $planillas;
        //dd($planillas);


        return view('insumos/reporte/costos_planilla/index', ['planillas' => $planillas, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]);
    }
    public function reporte_global_costos(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $fecha_desde = date("Y-m-d", strtotime($request['fecha_desde']));
        $fecha_hasta = date("Y-m-d", strtotime($request['fecha_hasta']));
        $planillas = DB::table('planilla_detalle as pd')
            ->join('planilla_cabecera as pc', 'pd.id_planilla_cabecera', 'pc.id')
            ->where('pc.aprobado', 1)
            ->where('pd.check', 1)
            ->where('pd.estado', 1)
            ->whereNotNull('pc.id_asiento_cabecera')
            ->groupby('pd.id_planilla_cabecera')
            //->sum('pd.precio')
            ->select('pc.fecha', 'pc.observacion', 'pc.id_hc_procedimiento', 'pd.precio', 'pc.id');
        //dd($planillas);
        if ($fecha_desde != null && $fecha_hasta != null) {
            $planillas = $planillas->whereBetween('pc.fecha', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        }
        if ($fecha_desde != null) {
            $planillas = $planillas->where('pc.fecha', '>=', $fecha_desde);
        }
        if ($fecha_hasta != null) {
            $planillas = $planillas->where('pc.fecha', '<=', $fecha_hasta);
        }
        $planillas = $planillas->get();

        $fecha_hoy = date('Y/m/d');

        Excel::create('Reporte_Global_Costos' . $fecha_hoy, function ($excel) use ($planillas) {

            $excel->sheet('Reporte Global de Costos', function ($sheet) use ($planillas) {
                $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");

                $fecha_hoy = date('Y/m/d');
                $sheet->mergeCells('A1:D2');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('Reporte Global de Costos');
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#D1E9F2');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('20');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $i = 3;

                $arrTitulos1 = ["Fecha", "Paciente", "Procedimiento" ,"Total Planilla"];

                $comienzo = 3;

                for ($i = 0; $i < count($arrTitulos1); $i++) {
                    $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrTitulos1, $i) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos1[$i]);
                        $cell->setBackground('#D1E9F2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;

                $acum_precio = 0;
                $acum_total =0;
                $arrValue = [];
                foreach ($planillas as $value) {

                    

                    //dd($acum_precio);
                    //$acum_precio += $value->precio;
                    $procedimiento_final = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id_hc_procedimiento)->first();

                    $detalles = Planilla_Detalle::where('id_planilla_cabecera',$value->id)->where('check',1)->where('estado',1)->sum('precio');

                    $acum_precio += $detalles;
                    $arrValue = [date('d-m-Y', strtotime($value->fecha)), $value->observacion, $procedimiento_final->procedimiento->nombre, $detalles];

                    for ($i = 0; $i < count($arrValue); $i++) {
                        $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrValue, $i) {
                            // manipulate the cel
                            $cell->setValue($arrValue[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;

                }
             
                    $arrTitulos2 = ["Total"];
                    for ($x = 0; $x < count($arrTitulos2); $x++) {
                        $sheet->cell("{$abec[$x+2]}{$comienzo}", function ($cell) use ($arrTitulos2, $x) {
                            // manipulate the cel
                            $cell->setValue($arrTitulos2[$x]);
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;

                    $arrValue2 = [];
                    $acum_total = 0;
                    $acum_total += $acum_precio;
                    $arrValue2 = [$acum_total];

                    for ($y = 0; $y < count($arrValue2); $y++) {
                        $sheet->cell("{$abec[$y+3]}". ($comienzo-1), function ($cell) use ($arrValue2, $y) {
                            // manipulate the cel
                            $cell->setValue($arrValue2[$y]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        
                    }
                    $comienzo++;


               
            });
        })->export('xlsx');
        /*
        $acum_precio = 0;
        foreach($planillas as $value){
            //dd($value);
            $detalles = Planilla_Detalle::where('id_planilla_cabecera',$value->id)->where('check',1)->where('estado',1)->get();
            //dd($detalles);
            foreach($detalles as $det){
                $acum_precio += $det->precio;
               
            }
            //dd($acum_precio);
            $procedimiento_final = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->id_hc_procedimiento)->first();
            dd($value->fecha,$value->observacion, $acum_precio,$procedimiento_final->procedimiento->nombre);
        }*/
       
    }
    public function reporte_uso_productos_proforma(Request $request)
    {
        //    dd($request->all());
        $productos = ReportesController::busqueda($request)->get();
    
        $fecha_d = date('Y/m/d');

        Excel::create('Uso_Productos_Proforma' . $fecha_d, function ($excel) use ($productos) {

            $excel->sheet('Uso de Productos Proforma', function ($sheet) use ($productos) {
                $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");

                $fecha_d = date('Y/m/d');

                $i = 1;

                $arrTitulos = ["codigo","nombre", "lote", "bodega", "serie", "cantidad", "precio", "descuento", "Pedido"];

                $comienzo = 1;

                for ($i = 0; $i < count($arrTitulos); $i++) {
                    $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrTitulos, $i) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos[$i]);
                        $cell->setBackground('#D1E9F2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;

                foreach ($productos as $value) {
                    $arrValue = [];
                    $nombre_proc = "";
                    $nombre_doc = "";
                    $precio_pedido = [];
                    $precio_total = [];
                
                    $id_pedido = $value->id_pedido;
                    $det_movimientos = InvDetMovimientos::where('id_pedido', $id_pedido)->where('serie', $value->serie)->first();
                    $precio_pedido = $det_movimientos->valor_unitario;

                    $precio_total =  $value->cantidad *  $value->total;

                    $arrValue = [ $value->codigo,$value->nombre_producto, $value->lote,$value->nombre_bodega, $value->serie . "-", $value->cantidad, $precio_pedido, $value->descuento ,$value->pedido];

                    for ($i = 0; $i < count($arrValue); $i++) {
                        $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrValue, $i) {
                            // manipulate the cel
                            $cell->setValue($arrValue[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }

                $comienzo++;


            });
        })->export('xlsx');
    }

    public function reporte_consumibles_proforma(Request $request)
    {
        //    dd($request->all());
        $consumibles = ReportesController::busqueda_consumibles($request)->get();
        $fecha_d = date('Y/m/d');

        Excel::create('Uso_Productos_Proforma' . $fecha_d, function ($excel) use ($consumibles) {

            $excel->sheet('Uso de Productos Proforma', function ($sheet) use ($consumibles) {
                $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");

                $fecha_d = date('Y/m/d');

                $i = 1;

                $arrTitulos = ["codigo","nombre", "lote", "bodega", "serie", "cantidad", "precio", "descuento", "Pedido"];

                $comienzo = 1;

                for ($i = 0; $i < count($arrTitulos); $i++) {
                    $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrTitulos, $i) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos[$i]);
                        $cell->setBackground('#D1E9F2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;

                foreach ($consumibles as $value) {
                    $arrValue = [];
                    $precio_total = [];

                    if (!is_null($value->nombreprov)) {
                        $nombre_proveedor = $value->nombreprov;
                    } else {
                        $nombre_proveedor = "N/A";
                    }
                    $precio_total = $value->cantidad *  $value->total;

                    $arrValue = [ $value->codigo,$value->nombre_producto, $value->lote,$value->nombre_bodega, $value->serie . "-", $value->cantidad, $precio_total, $value->descuento ,$value->pedido];

                    for ($i = 0; $i < count($arrValue); $i++) {
                        $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrValue, $i) {
                            // manipulate the cel
                            $cell->setValue($arrValue[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }

                $comienzo++;

              
            });
        })->export('xlsx');
    }
    public function nuevaproforma_pdf($id){
        $proforma = Proforma_Venta_Cab::find($id);
        $vistaurl = "nuevo_formato_proforma.pdf_proforma";
        $view = \View::make($vistaurl, compact('proforma'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true,  'chroot' => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Proforma de Venta-' . $id . '.pdf');


    }
    public function reclacular_detalle_planilla($id_planilla_cabecera){
        $cabecera = Planilla::find($id_planilla_cabecera);
        $detalles = $cabecera->detalles;
        foreach ($detalles as $det){
            if($det->precio == null){
                $det_mov = InvDetMovimientos::where('id_movimiento_paciente',$det->id_movimiento_paciente)->first();
                if($det_mov != null){
                    $input = [
                        'precio' => $det_mov->valor_unitario,
                        'ip_creacion' => 'REPRO_VU_15_1_24',
                    ];
                    $det->update($input);
                }

            }
        }
        return "Estamos Rulay";



    }

    public function index_stock_minimo(){
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::find($id_empresa);
     
        //dd($inventario);
        $inventario = DB::select("SELECT * from inv_inventario i join producto p on i.id_producto = p.id 
        where i.existencia < i.existencia_min and i.id_empresa = '$id_empresa'  group by id_producto" );
        $fecha_hoy = date('Y-m-d');
        $stock = DB::select("SELECT * from producto p where p.cantidad < p.minimo");

        return view('insumos/reporte/stock_minimo/stock_minimo',['inventario' =>$inventario,'stock' => $stock,'empresa' => $empresa]);
    }
    public function reporteProdCaducados(Request $request)
    {
        $fecha_hoy = date('Y-m-d');
        $hoy = date('Y-m-d');
        $en_3_meses = date('Y-m-d', strtotime('+3 months'));
        $en_6_meses = date('Y-m-d', strtotime('+6 months'));

        $id_empresa = Session::get('id_empresa');
        $estado = $request->estado;

        if (request()->isMethod('post')) {
            session([
                'filtro_q' => request('q'),
                'filtro_estado' => request('estado')
            ]);
        }

        $q = session('filtro_q');
        $estado = session('filtro_estado');


        $query = InvDetMovimientos::join('inv_inventario as ii', 'ii.id', '=', 'inv_det_movimientos.id_inv_inventario')
            ->join('inv_inventario_serie as iis', 'iis.id_inv_inventario', '=', 'ii.id')
            ->join('producto as p', 'p.id', '=', 'iis.id_producto')
            ->join('pedido as pe', 'pe.id', '=', 'inv_det_movimientos.id_pedido')
            ->join('proveedor as pr', 'pr.id', '=', 'pe.id_proveedor')
            ->join('marca as m', 'm.id', '=', 'p.id_marca')
            ->join('bodega as b', 'b.id', '=', 'iis.id_bodega')
            ->where('iis.id_empresa', $id_empresa)
            ->where('iis.existencia', '>=', 1)
            ->select([
                'p.codigo',
                'p.nombre',
                'p.descripcion',
                'iis.serie',
                'pe.pedido',
                'b.nombre as bodega',
                'pr.razonsocial as proveedor',
                'm.nombre as marca',
                'iis.existencia as cantidad',
                'iis.fecha_vence',
            ])
            ->selectRaw("
            CASE
                WHEN DATE(iis.fecha_vence) < ? THEN 'Producto Caducado'
                WHEN DATE(iis.fecha_vence) <= ? THEN 'Caduca en los próximos 3 meses'
                WHEN DATE(iis.fecha_vence) <= ? THEN 'Caduca en los próximos 6 meses'
                ELSE 'Producto en buen estado'
            END AS estado", [$hoy, $en_3_meses, $en_6_meses])
            ->selectRaw("
            CASE
                WHEN DATE(iis.fecha_vence) < ? THEN '#dc3545'
                WHEN DATE(iis.fecha_vence) <= ? THEN '#ffc107'
                WHEN DATE(iis.fecha_vence) <= ? THEN '#0dcaf0'
                ELSE '#198754'
            END AS estado_color", [$hoy, $en_3_meses, $en_6_meses]);

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('p.codigo', 'like', "%$q%")
                    ->orWhere('p.nombre', 'like', "%$q%")
                    ->orWhere('iis.serie', 'like', "%$q%");
            });
        }
        if ($estado) {

            if ($estado === 'caducado') {
                $query->where('iis.fecha_vence', '<', $hoy);
            } elseif ($estado === '3meses') {
                $query->whereBetween('iis.fecha_vence', [$hoy, $en_3_meses]);
            } elseif ($estado === '6meses') {
                $query->whereBetween('iis.fecha_vence', [$en_3_meses, $en_6_meses]);
            } elseif ($estado === 'ok') {
                $query->where('iis.fecha_vence', '>', $en_6_meses);
            }
        }

        $caducado = $query
            ->groupBy('iis.serie', 'iis.id_producto', 'iis.id_bodega')
            ->paginate(25)
            ->appends(request()->query());

        return view('insumos.reporte.caducado.index_caducados', ['data' => $caducado, 'estado' => $estado]);
    }
}
